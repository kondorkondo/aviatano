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
        // Log 
        Log::info('ZenoPay Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        // Validate API key from header
        $apiKey = $request->header('X-API-Key');
        if (!$this->zenoPayService->validateWebhookSignature($apiKey)) {
            Log::warning('ZenoPay Webhook: Invalid API key', [
                'received_key' => $apiKey
            ]);
            
            return response('Invalid API key', 403);
        }

        // Get the payload
        $payload = $request->all();
        
        if (empty($payload)) {
            Log::warning('ZenoPay Webhook: Empty payload');
            return response('Empty payload', 400);
        }

        // Process the webhook
        $processed = $this->zenoPayService->processWebhook($payload);

        if ($processed) {
            Log::info('ZenoPay Webhook: Successfully processed', [
                'order_id' => $payload['order_id'] ?? 'unknown'
            ]);
            return response('OK', 200);
        } else {
            Log::error('ZenoPay Webhook: Failed to process', [
                'payload' => $payload
            ]);
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


}

