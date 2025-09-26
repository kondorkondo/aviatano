<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ZenoPayService;
use Illuminate\Support\Facades\Log;

class ZenoPayController extends Controller
{
    private $zenoPayService;

    public function __construct(ZenoPayService $zenoPayService)
    {
        $this->zenoPayService = $zenoPayService;
    }

    /**
     * Handle ZenoPay webhook notifications
     *
     * @param Request $request
     * @return Response
     */
public function webhook(Request $request): Response
{
    \Log::info('=== ZENOPAY WEBHOOK HEADERS ===', [
        'all_headers' => $request->headers->all(),
        'received_api_key' => $request->header('X-API-Key'),
        'expected_api_key' => config('zenopay.api_key')
    ]);
    
    // Temporarily bypass API key validation for testing
    $apiKey = $request->header('X-API-Key');
    \Log::info('API Key Debug', [
        'received' => $apiKey,
        'expected' => config('zenopay.api_key'),
        'match' => $apiKey === config('zenopay.api_key')
    ]);
    
    // TEMPORARY: Bypass API key validation to test webhook processing
    if (app()->environment('local') || $request->get('bypass_key')) {
        \Log::info('Bypassing API key validation for testing');
    } else {
        if (!$this->zenoPayService->validateWebhookSignature($apiKey)) {
            \Log::warning('ZenoPay Webhook: Invalid API key', [
                'received' => $apiKey,
                'expected' => config('zenopay.api_key')
            ]);
            return response('Invalid API key', 403);
        }
    }
    
    // Rest of your webhook processing...
    $payload = $request->all();
    \Log::info('Webhook Payload Received', $payload);
    
    $processed = $this->zenoPayService->processWebhook($payload);
    
    if ($processed) {
        return response('OK', 200);
    } else {
        return response('Processing failed', 500);
    }
}  

    /**
     * Check order status 
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        
        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID is required'
            ], 400);
        }

        $result = $this->zenoPayService->checkOrderStatus($orderId);

        return response()->json($result);
    }




public function processWebhook(array $webhookData): bool
{
    try {
        Log::info('>>> processWebhook method started');
        
        $orderId = $webhookData['order_id'] ?? null;
        $paymentStatus = $webhookData['payment_status'] ?? null;
        $reference = $webhookData['reference'] ?? null;

        Log::info('Webhook data extracted', [
            'order_id' => $orderId,
            'payment_status' => $paymentStatus,
            'reference' => $reference
        ]);

        if (!$orderId || !$paymentStatus) {
            Log::warning('ZenoPay Webhook: Missing required fields');
            return false;
        }

        // Find transaction by order_id using DB facade to avoid model issues
        Log::info('Looking for transaction with order_id: ' . $orderId);
        
        $transaction = \Illuminate\Support\Facades\DB::table('transactions')
            ->where('transactionno', $orderId)
            ->first();

        if (!$transaction) {
            Log::warning('ZenoPay Webhook: Transaction not found');
            return false;
        }

        Log::info('Transaction FOUND', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->userid,
            'amount' => $transaction->amount,
            'current_status' => $transaction->status
        ]);

        // Update transaction based on payment status
        switch ($paymentStatus) {
            case 'COMPLETED':
                Log::info('Processing COMPLETED payment...');
                $this->handleSuccessfulPayment((array)$transaction, $reference);
                break;
            case 'FAILED':
            case 'CANCELLED':
                Log::info('Processing FAILED payment...');
                $this->handleFailedPayment((array)$transaction, $reference);
                break;
            default:
                Log::info('Unknown payment status: ' . $paymentStatus);
                break;
        }

        Log::info('<<< processWebhook method completed successfully');
        return true;

    } catch (\Exception $e) {
        Log::error('ZenoPay Webhook Processing Exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}
}
