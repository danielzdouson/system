<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\Loan;
use App\Models\AdminBankDetail;
use App\Services\PesapalService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    private PesapalService $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        $this->middleware('auth');
        $this->middleware('member');
        $this->pesapalService = $pesapalService;
    }

    /**
     * Display payments page
     */
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        // Get member's payment history
        $payments = PaymentTransaction::forMember($member->id)
            ->latest()
            ->paginate(20);

        // Get active loans for loan payments
        $activeLoans = Loan::where('member_id', $member->id)
            ->where('status', 'active')
            ->get();

        // Get admin bank details
        $bankDetails = AdminBankDetail::active()->get();

        return view('member.payments', compact(
            'payments',
            'activeLoans',
            'bankDetails',
            'member'
        ));
    }

    /**
     * Show loan payment form
     */
    public function loanPayment($loanId)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $loan = Loan::where('id', $loanId)
            ->where('member_id', $member->id)
            ->firstOrFail();

        // Get admin bank details
        $bankDetails = AdminBankDetail::active()->get();

        return view('member.loan-payment', compact(
            'loan',
            'bankDetails',
            'member'
        ));
    }

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:loan_payment,fine_payment,savings_deposit,education,other',
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:mobile_money,card,bank_transfer',
            'phone_number' => 'required_if:payment_method,mobile_money|regex:/^256\d{9}$/',
            'mobile_network' => 'required_if:payment_method,mobile_money|in:MTN,AIRTEL',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'success' => false,
                'error' => 'No member account linked to your user account.'
            ], 403);
        }

        // Validate amount for loan payments
        if ($request->payment_type === 'loan_payment' && $request->has('loan_id')) {
            $loan = Loan::where('id', $request->loan_id)
                ->where('member_id', $member->id)
                ->first();

            if (!$loan) {
                return response()->json([
                    'success' => false,
                    'error' => 'Loan not found.'
                ], 404);
            }

            if ($request->amount > $loan->balance) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment amount cannot exceed outstanding balance.'
                ], 400);
            }
        }

        // Get admin bank details for bank transfer
        $adminBankDetails = null;
        if ($request->payment_method === 'bank_transfer') {
            $bankDetails = AdminBankDetail::active()->bankTransfer()->first();
            if ($bankDetails) {
                $adminBankDetails = [
                    'bank_name' => $bankDetails->bank_name,
                    'account_name' => $bankDetails->account_name,
                    'account_number' => $bankDetails->account_number,
                    'branch' => $bankDetails->branch,
                    'swift_code' => $bankDetails->swift_code,
                ];
            }
        }

        // Create payment transaction record
        $transaction = PaymentTransaction::create([
            'member_id' => $member->id,
            'amount' => $request->amount,
            'currency' => 'UGX',
            'payment_type' => $request->payment_type,
            'payment_method' => $request->payment_method,
            'mobile_network' => $request->mobile_network,
            'phone_number' => $request->phone_number,
            'transaction_reference' => null, // Will be set by Pesapal
            'pesapal_tracking_id' => null,
            'status' => 'pending',
            'related_id' => $request->loan_id ?? null,
            'related_type' => $request->payment_type === 'loan_payment' ? Loan::class : null,
            'admin_bank_details' => $adminBankDetails,
            'notes' => $request->notes,
        ]);

        // Submit payment to Pesapal
        $callbackUrl = route('member.payment.callback');
        
        $paymentData = [
            'amount' => $request->amount,
            'currency' => 'UGX',
            'description' => $this->getPaymentDescription($request->payment_type),
            'email' => $user->email,
            'phone_number' => $request->phone_number,
            'callback_url' => $callbackUrl,
        ];

        $result = $this->pesapalService->submitPayment($paymentData);

        if (!$result['success']) {
            $transaction->status = 'failed';
            $transaction->save();

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Payment submission failed',
            ], 500);
        }

        // Update transaction with Pesapal details
        $transaction->transaction_reference = $result['reference'];
        $transaction->pesapal_tracking_id = $result['tracking_id'];
        $transaction->status = 'processing';
        $transaction->save();

        return response()->json([
            'success' => true,
            'redirect_url' => $result['redirect_url'],
            'tracking_id' => $result['tracking_id'],
            'reference' => $result['reference'],
        ]);
    }

    /**
     * Handle payment callback from Pesapal
     */
    public function callback(Request $request)
    {
        $trackingId = $request->query('pesapal_transaction_tracking_id');
        $merchantReference = $request->query('pesapal_merchant_reference');

        if (!$trackingId || !$merchantReference) {
            return redirect()->route('member.payments')
                ->with('error', 'Invalid payment callback parameters.');
        }

        // Find the transaction
        $transaction = PaymentTransaction::where('transaction_reference', $merchantReference)
            ->first();

        if (!$transaction) {
            return redirect()->route('member.payments')
                ->with('error', 'Transaction not found.');
        }

        // Query payment status from Pesapal
        $statusResult = $this->pesapalService->queryPaymentStatus($trackingId, $merchantReference);

        if ($statusResult['success']) {
            $transaction->status = $statusResult['status'];
            
            if ($statusResult['status'] === 'completed') {
                $transaction->completed_at = now();
            }
            
            $transaction->save();
        }

        // Redirect based on status
        if ($transaction->status === 'completed') {
            return redirect()->route('member.payments')
                ->with('success', 'Payment completed successfully!');
        } elseif ($transaction->status === 'processing') {
            return redirect()->route('member.payments')
                ->with('info', 'Payment is being processed. Please check back later.');
        } else {
            return redirect()->route('member.payments')
                ->with('error', 'Payment failed or was cancelled.');
        }
    }

    /**
     * Get payment history
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            abort(403, 'No member account linked to your user account.');
        }

        $query = PaymentTransaction::forMember($member->id);

        // Apply filters
        if ($request->payment_type) {
            $query->byPaymentType($request->payment_type);
        }

        if ($request->status) {
            $query->byStatus($request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        return view('member.payment-history', compact('payments'));
    }

    /**
     * Get payment description
     */
    private function getPaymentDescription(string $paymentType): string
    {
        return match($paymentType) {
            'loan_payment' => 'Loan Repayment',
            'fine_payment' => 'Fine Payment',
            'savings_deposit' => 'Savings Deposit',
            'education' => 'Education Payment',
            'other' => 'Other Payment',
            default => 'Payment',
        };
    }
}
