<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\AdminBankDetail;
use App\Services\PesapalService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    private PesapalService $pesapalService;

    public function __construct(PesapalService $pesapalService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->pesapalService = $pesapalService;
    }

    /**
     * Display payment dashboard
     */
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['member', 'related']);

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

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_reference', 'like', '%' . $request->search . '%')
                  ->orWhere('pesapal_tracking_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($mq) use ($request) {
                      $mq->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $payments = $query->latest()->paginate(50);

        // Calculate statistics
        $stats = [
            'total' => PaymentTransaction::count(),
            'pending' => PaymentTransaction::byStatus('pending')->count(),
            'processing' => PaymentTransaction::byStatus('processing')->count(),
            'completed' => PaymentTransaction::byStatus('completed')->count(),
            'failed' => PaymentTransaction::byStatus('failed')->count(),
            'total_amount' => PaymentTransaction::byStatus('completed')->sum('amount'),
        ];

        return view('admin.payments', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = PaymentTransaction::with(['member', 'related'])->findOrFail($id);

        // Query current status from Pesapal
        if ($payment->pesapal_tracking_id && $payment->transaction_reference) {
            $statusResult = $this->pesapalService->queryPaymentStatus(
                $payment->pesapal_tracking_id,
                $payment->transaction_reference
            );

            if ($statusResult['success']) {
                $payment->pesapal_status = $statusResult['pesapal_status'];
            }
        }

        return view('admin.payment-details', compact('payment'));
    }

    /**
     * Refresh payment status from Pesapal
     */
    public function refreshStatus($id)
    {
        $payment = PaymentTransaction::findOrFail($id);

        if (!$payment->pesapal_tracking_id || !$payment->transaction_reference) {
            return response()->json([
                'success' => false,
                'error' => 'Payment does not have Pesapal tracking information.'
            ], 400);
        }

        $statusResult = $this->pesapalService->queryPaymentStatus(
            $payment->pesapal_tracking_id,
            $payment->transaction_reference
        );

        if (!$statusResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $statusResult['error'] ?? 'Failed to query payment status.'
            ], 500);
        }

        // Update payment status
        $payment->status = $statusResult['status'];
        
        if ($statusResult['status'] === 'completed') {
            $payment->completed_at = now();
        }
        
        $payment->save();

        return response()->json([
            'success' => true,
            'status' => $statusResult['status'],
            'pesapal_status' => $statusResult['pesapal_status'],
        ]);
    }

    /**
     * Display bank details management
     */
    public function bankDetails()
    {
        $bankDetails = AdminBankDetail::all();

        return view('admin.bank-details', compact('bankDetails'));
    }

    /**
     * Store new bank details
     */
    public function storeBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:20',
            'mobile_money_provider' => 'nullable|in:MTN,AIRTEL',
            'mobile_money_number' => 'nullable|string|max:20|required_if:mobile_money_provider,MTN,AIRTEL',
            'instructions' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // If setting as default, remove default from others
        if ($request->is_default) {
            AdminBankDetail::query()->update(['is_default' => false]);
        }

        AdminBankDetail::create([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'swift_code' => $request->swift_code,
            'is_active' => true,
            'is_default' => $request->is_default ?? false,
            'mobile_money_provider' => $request->mobile_money_provider,
            'mobile_money_number' => $request->mobile_money_number,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('admin.payments.bank-details')
            ->with('success', 'Bank details added successfully.');
    }

    /**
     * Update bank details
     */
    public function updateBankDetails(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:20',
            'mobile_money_provider' => 'nullable|in:MTN,AIRTEL',
            'mobile_money_number' => 'nullable|string|max:20|required_if:mobile_money_provider,MTN,AIRTEL',
            'instructions' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $bankDetail = AdminBankDetail::findOrFail($id);

        // If setting as default, remove default from others
        if ($request->is_default) {
            AdminBankDetail::where('id', '!=', $id)->update(['is_default' => false]);
        }

        $bankDetail->update([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'swift_code' => $request->swift_code,
            'is_default' => $request->is_default ?? false,
            'is_active' => $request->is_active ?? true,
            'mobile_money_provider' => $request->mobile_money_provider,
            'mobile_money_number' => $request->mobile_money_number,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('admin.payments.bank-details')
            ->with('success', 'Bank details updated successfully.');
    }

    /**
     * Get bank details for editing
     */
    public function editBankDetails($id)
    {
        $bankDetail = AdminBankDetail::findOrFail($id);
        return response()->json($bankDetail);
    }

    /**
     * Delete bank details
     */
    public function deleteBankDetails($id)
    {
        $bankDetail = AdminBankDetail::findOrFail($id);
        $bankDetail->delete();

        return redirect()->route('admin.payments.bank-details')
            ->with('success', 'Bank details deleted successfully.');
    }

    /**
     * Export payments report
     */
    public function export(Request $request)
    {
        $query = PaymentTransaction::with(['member']);

        // Apply same filters as index
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

        $payments = $query->latest()->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID',
                'Reference',
                'Tracking ID',
                'Member',
                'Amount',
                'Currency',
                'Type',
                'Method',
                'Status',
                'Created At',
                'Completed At',
            ]);

            // Data rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->transaction_reference,
                    $payment->pesapal_tracking_id,
                    $payment->member->first_name . ' ' . $payment->member->last_name,
                    $payment->amount,
                    $payment->currency,
                    $payment->payment_type_label,
                    $payment->payment_method_label,
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->completed_at ? $payment->completed_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Handle Pesapal IPN (Instant Payment Notification)
     */
    public function handleIpn(Request $request)
    {
        Log::info('Pesapal IPN received', $request->all());

        $trackingId = $request->query('pesapal_transaction_tracking_id');
        $merchantReference = $request->query('pesapal_merchant_reference');
        $notificationType = $request->query('pesapal_notification_type');

        if (!$trackingId || !$merchantReference) {
            Log::error('Invalid IPN data - missing tracking ID or reference', [
                'tracking_id' => $trackingId,
                'reference' => $merchantReference,
            ]);
            
            // Return error response to Pesapal
            return response('Invalid IPN data', 400);
        }

        // Process the IPN using the Pesapal service
        $result = $this->pesapalService->processIpn([
            'pesapal_transaction_tracking_id' => $trackingId,
            'pesapal_merchant_reference' => $merchantReference,
            'pesapal_notification_type' => $notificationType,
        ]);

        if ($result['success']) {
            Log::info('IPN processed successfully', [
                'tracking_id' => $trackingId,
                'reference' => $merchantReference,
                'status' => $result['status'],
            ]);
            
            // Return success response to Pesapal
            return response('OK', 200);
        } else {
            Log::error('IPN processing failed', [
                'tracking_id' => $trackingId,
                'reference' => $merchantReference,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
            
            // Return error response to Pesapal
            return response('Processing failed', 500);
        }
    }
}
