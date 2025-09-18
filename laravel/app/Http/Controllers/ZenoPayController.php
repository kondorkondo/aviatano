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
        // Log the incoming webhook for debugging
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
     * Check order status (for testing/debugging)
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

    /**
     * Create test order (for testing/debugging)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTestOrder(Request $request)
    {
        $orderData = $this->zenoPayService->prepareOrderData(
            $request->input('user_id', 1),
            $request->input('email', 'test@example.com'),
            $request->input('name', 'Test User'),
            $request->input('phone', '0744963858'),
            $request->input('amount', 1000)
        );

        $result = $this->zenoPayService->createOrder($orderData);

        return response()->json($result);
    }
}

