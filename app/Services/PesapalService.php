<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentTransaction;

class PesapalService
{
    private string $consumerKey;
    private string $consumerSecret;
    private string $environment;
    private string $ipnUrl;

    private array $apiUrls = [
        'sandbox' => 'https://cybqa.pesapal.com/pesapalv3/api',
        'live' => 'https://pay.pesapal.com/v3/api',
    ];

    public function __construct()
    {
        $this->consumerKey = config('services.pesapal.consumer_key');
        $this->consumerSecret = config('services.pesapal.consumer_secret');
        $this->environment = config('services.pesapal.environment', 'sandbox');
        $this->ipnUrl = config('services.pesapal.ipn_url');
    }

    /**
     * Get authentication token from Pesapal
     */
    private function getAuthToken(): ?string
    {
        $apiUrl = $this->apiUrls[$this->environment];
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$apiUrl}/Auth/RequestToken", [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            Log::error('Failed to get Pesapal auth token', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Pesapal auth token error', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Register IPN URL with Pesapal
     */
    private function registerIPN(string $token): ?string
    {
        $apiUrl = $this->apiUrls[$this->environment];
        
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->post("{$apiUrl}/URLSetup/RegisterIPN", [
                'url' => $this->ipnUrl,
                'ipn_notification_type' => 'GET',
            ]);

            if ($response->successful()) {
                return $response->json('ipn_id');
            }

            Log::error('Failed to register IPN', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('IPN registration error', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Submit a payment transaction to Pesapal
     */
    public function submitPayment(array $paymentData): array
    {
        $apiUrl = $this->apiUrls[$this->environment];
        
        // Get auth token
        $token = $this->getAuthToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate with Pesapal',
            ];
        }
        
        // Register IPN (if not already registered)
        $ipnId = $this->registerIPN($token);
        
        // Generate unique reference
        $reference = $this->generateReference();
        
        // Prepare payment data according to Pesapal API 3.0
        $data = [
            'id' => $reference,
            'currency' => $paymentData['currency'] ?? 'UGX',
            'amount' => number_format($paymentData['amount'], 2, '.', ''),
            'description' => $paymentData['description'] ?? 'Payment',
            'callback_url' => $paymentData['callback_url'],
            'notification_id' => $ipnId,
            'billing_address' => [
                'email_address' => $paymentData['email'],
                'phone_number' => $paymentData['phone_number'] ?? null,
                'country_code' => 'UG',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->post("{$apiUrl}/Transactions/SubmitOrderRequest", $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'reference' => $reference,
                    'redirect_url' => $response->json('redirect_url') ?? null,
                    'tracking_id' => $response->json('order_tracking_id') ?? null,
                    'data' => $response->json(),
                ];
            }

            Log::error('Pesapal payment submission failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment submission failed',
                'details' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Pesapal payment submission error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment submission error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query payment status from Pesapal
     */
    public function queryPaymentStatus(string $trackingId, string $merchantReference): array
    {
        $apiUrl = $this->apiUrls[$this->environment];
        
        // Get auth token
        $token = $this->getAuthToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate with Pesapal',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->get("{$apiUrl}/Transactions/GetTransactionStatus", [
                'orderTrackingId' => $trackingId,
                'merchantReference' => $merchantReference,
            ]);

            if ($response->successful()) {
                $status = $response->json('status');
                $paymentStatus = $this->mapPesapalStatus($status);

                return [
                    'success' => true,
                    'status' => $paymentStatus,
                    'pesapal_status' => $status,
                    'data' => $response->json(),
                ];
            }

            Log::error('Pesapal status query failed', [
                'tracking_id' => $trackingId,
                'reference' => $merchantReference,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Status query failed',
                'details' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Pesapal status query error', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Status query error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process IPN notification from Pesapal
     */
    public function processIpn(array $ipnData): array
    {
        $trackingId = $ipnData['pesapal_transaction_tracking_id'] ?? null;
        $merchantReference = $ipnData['pesapal_merchant_reference'] ?? null;

        if (!$trackingId || !$merchantReference) {
            return [
                'success' => false,
                'error' => 'Invalid IPN data',
            ];
        }

        // Find the payment transaction
        $transaction = PaymentTransaction::where('transaction_reference', $merchantReference)
            ->where('pesapal_tracking_id', $trackingId)
            ->first();

        if (!$transaction) {
            Log::warning('Payment transaction not found for IPN', [
                'tracking_id' => $trackingId,
                'reference' => $merchantReference,
            ]);

            return [
                'success' => false,
                'error' => 'Transaction not found',
            ];
        }

        // Query actual status from Pesapal
        $statusResult = $this->queryPaymentStatus($trackingId, $merchantReference);

        if (!$statusResult['success']) {
            return [
                'success' => false,
                'error' => 'Failed to query payment status',
            ];
        }

        // Update transaction status
        $transaction->status = $statusResult['status'];
        
        if ($statusResult['status'] === 'completed') {
            $transaction->completed_at = now();
            
            // Process the payment based on type
            $this->processSuccessfulPayment($transaction);
        }

        $transaction->save();

        return [
            'success' => true,
            'transaction' => $transaction,
            'status' => $statusResult['status'],
        ];
    }

    /**
     * Process a successful payment
     */
    private function processSuccessfulPayment(PaymentTransaction $transaction): void
    {
        switch ($transaction->payment_type) {
            case 'loan_payment':
                $this->processLoanPayment($transaction);
                break;
            case 'fine_payment':
                $this->processFinePayment($transaction);
                break;
            case 'savings_deposit':
                $this->processSavingsDeposit($transaction);
                break;
            default:
                // Handle other payment types
                break;
        }
    }

    /**
     * Process loan payment
     */
    private function processLoanPayment(PaymentTransaction $transaction): void
    {
        $loanId = $transaction->related_id;
        if (!$loanId) return;

        $loan = \App\Models\Loan::find($loanId);
        if (!$loan) return;

        // Create loan repayment record
        $repayment = \App\Models\LoanRepayment::create([
            'loan_id' => $loanId,
            'member_id' => $transaction->member_id,
            'amount' => $transaction->amount,
            'principal_component' => $transaction->amount, // Simplified calculation
            'interest_component' => 0,
            'method' => $transaction->payment_method === 'mobile_money' ? 'mobile_money' : 'bank_transfer',
            'reference' => $transaction->transaction_reference,
            'paid_at' => now(),
            'received_by' => auth()->id() ?? 1, // System user
            'notes' => 'Online payment via Pesapal',
        ]);

        // Update loan balance
        $loan->balance -= $transaction->amount;
        if ($loan->balance <= 0) {
            $loan->balance = 0;
            $loan->status = 'completed';
        }
        $loan->save();

        Log::info('Loan payment processed successfully', [
            'transaction_id' => $transaction->id,
            'loan_id' => $loanId,
            'amount' => $transaction->amount,
        ]);
    }

    /**
     * Process fine payment
     */
    private function processFinePayment(PaymentTransaction $transaction): void
    {
        $fineId = $transaction->related_id;
        if (!$fineId) return;

        $fine = \App\Models\Fine::find($fineId);
        if (!$fine) return;

        // Create fine payment record
        $payment = \App\Models\FinePayment::create([
            'fine_id' => $fineId,
            'amount' => $transaction->amount,
            'payment_method' => $transaction->payment_method === 'mobile_money' ? 'mobile_money' : 'bank_transfer',
            'transaction_reference' => $transaction->transaction_reference,
            'payment_date' => now(),
            'notes' => 'Online payment via Pesapal',
            'received_by' => auth()->id() ?? 1,
        ]);

        // Update fine status
        if ($transaction->amount >= $fine->amount) {
            $fine->status = 'paid';
        }
        $fine->save();

        Log::info('Fine payment processed successfully', [
            'transaction_id' => $transaction->id,
            'fine_id' => $fineId,
            'amount' => $transaction->amount,
        ]);
    }

    /**
     * Process savings deposit
     */
    private function processSavingsDeposit(PaymentTransaction $transaction): void
    {
        // Create deposit record
        $deposit = \App\Models\Deposit::create([
            'member_id' => $transaction->member_id,
            'amount' => $transaction->amount,
            'type' => 'savings',
            'description' => 'Online deposit via Pesapal',
            'reference' => $transaction->transaction_reference,
            'created_by' => auth()->id() ?? 1,
        ]);

        Log::info('Savings deposit processed successfully', [
            'transaction_id' => $transaction->id,
            'deposit_id' => $deposit->id,
            'amount' => $transaction->amount,
        ]);
    }

    /**
     * Map Pesapal status to internal status
     */
    private function mapPesapalStatus(string $pesapalStatus): string
    {
        return match(strtolower($pesapalStatus)) {
            'completed', 'success' => 'completed',
            'pending', 'processing' => 'processing',
            'failed', 'cancelled' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Generate unique transaction reference
     */
    private function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Get Pesapal payment page URL
     */
    public function getPaymentPageUrl(string $trackingId): string
    {
        $baseUrl = $this->environment === 'sandbox' 
            ? 'https://cybqa.pesapal.com/pesapaliframe/iframe.aspx' 
            : 'https://pay.pesapal.com/iframe/iframe.aspx';
        
        return "{$baseUrl}?pesapal_transaction_tracking_id={$trackingId}";
    }
}
