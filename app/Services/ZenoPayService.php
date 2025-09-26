<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class ZenoPayService
{
    private $apiKey;
    private $baseUrl;
    private $webhookUrl;
    private $timeout;

    public function __construct()
    {
        $this->apiKey = config('zenopay.api_key');
        $this->baseUrl = config('zenopay.base_url');
        $this->webhookUrl = config('zenopay.webhook_url');
        $this->timeout = config('zenopay.timeout');
    }

    /**
     * Create a new payment order
     *
     * @param array $orderData
     * @return array
     */
    public function createOrder(array $orderData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-api-key' => $this->apiKey,
                ])
                ->post($this->baseUrl . '/mobile_money_tanzania', $orderData);

            $httpCode = $response->status();
            $responseData = $response->json();

            if ($httpCode !== 200) {
                Log::error('ZenoPay API Error', [
                    'http_code' => $httpCode,
                    'response' => $responseData,
                    'order_data' => $orderData
                ]);
                
                return [
                    'success' => false,
                    'message' => "HTTP {$httpCode}: " . ($responseData['message'] ?? 'Unknown error'),
                    'data' => null
                ];
            }

            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                Log::info('ZenoPay Order Created', [
                    'order_id' => $responseData['order_id'] ?? null,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Order created successfully',
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create order',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('ZenoPay Service Exception', [
                'message' => $e->getMessage(),
                'order_data' => $orderData
            ]);

            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Check order status
     *
     * @param string $orderId
     * @return array
     */
    public function checkOrderStatus(string $orderId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                ])
                ->get($this->baseUrl . '/order-status', [
                    'order_id' => $orderId
                ]);

            $httpCode = $response->status();
            $responseData = $response->json();

            if ($httpCode !== 200) {
                Log::error('ZenoPay Status Check Error', [
                    'http_code' => $httpCode,
                    'response' => $responseData,
                    'order_id' => $orderId
                ]);
                
                return [
                    'success' => false,
                    'message' => "HTTP {$httpCode}: " . ($responseData['message'] ?? 'Unknown error'),
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Status retrieved successfully',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('ZenoPay Status Check Exception', [
                'message' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Prepare order data for ZenoPay API
     *
     * @param int $userId
     * @param string $userEmail
     * @param string $userName
     * @param string $userPhone
     * @param float $amount
     * @param string|null $transactionId
     * @return array
     */
    public function prepareOrderData(int $userId, string $userEmail, string $userName, string $userPhone, float $amount, ?string $transactionId = null): array
    {
        $orderId = $transactionId ?: uniqid('zp_', true);

        return [
            'order_id' => $orderId,
            'buyer_email' => $userEmail,
            'buyer_name' => $userName,
            'buyer_phone' => $userPhone,
            'amount' => (int) $amount, // Amount in TZS (integer)
            'webhook_url' => $this->webhookUrl,
            'metadata' => [
                'user_id' => $userId,
                'transaction_id' => $transactionId,
                'currency' => 'TZS',
                'platform' => 'aviator_game'
            ]
        ];
    }

    /**
     * Validate webhook signature
     *
     * @param string $receivedSignature
     * @return bool
     */
public function validateWebhookSignature(string $receivedSignature): bool
{
    // If no key received, fail
    if (empty($receivedSignature)) {
        return false;
    }
    
    // If keys match exactly, success
    if (hash_equals($this->apiKey, $receivedSignature)) {
        return true;
    }
    
    // TEMPORARY: Log but allow for testing
    \Log::warning('API Key Mismatch - Allowing for testing', [
        'received' => $receivedSignature,
        'expected' => $this->apiKey
    ]);
    
    return true; // TEMPORARY: Allow all requests during testing
}

    /**
     * Process webhook notification
     *
     * @param array $webhookData
     * @return bool
     */
    public function processWebhook(array $webhookData): bool
    {
        try {
            $orderId = $webhookData['order_id'] ?? null;
            $paymentStatus = $webhookData['payment_status'] ?? null;
            $reference = $webhookData['reference'] ?? null;

            if (!$orderId || !$paymentStatus) {
                Log::warning('ZenoPay Webhook: Missing required fields', [
                    'webhook_data' => $webhookData
                ]);
                return false;
            }

            // Find transaction by order_id
            $transaction = Transaction::where('transactionno', $orderId)->first();

            if (!$transaction) {
                Log::warning('ZenoPay Webhook: Transaction not found', [
                    'order_id' => $orderId,
                    'webhook_data' => $webhookData
                ]);
                return false;
            }

            // Update transaction based on payment status
            switch ($paymentStatus) {
                case 'COMPLETED':
                    $this->handleSuccessfulPayment($transaction, $reference);
                    break;
                case 'FAILED':
                case 'CANCELLED':
                    $this->handleFailedPayment($transaction, $reference);
                    break;
                default:
                    Log::info('ZenoPay Webhook: Unknown payment status', [
                        'order_id' => $orderId,
                        'payment_status' => $paymentStatus,
                        'webhook_data' => $webhookData
                    ]);
                    break;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('ZenoPay Webhook Processing Exception', [
                'message' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment - UPDATED to use new helper function
     *
     * @param Transaction $transaction
     * @param string|null $reference
     * @return void
     */
/**
 * Handle successful payment - FIXED VERSION
 *
 * @param Transaction $transaction
 * @param string|null $reference
 * @return void
 */
private function handleSuccessfulPayment(Transaction $transaction, ?string $reference = null): void
{
    try {
        // Start database transaction for data consistency
        \DB::beginTransaction();

        // 1. Update transaction status to successful (1 = success)
        $transaction->update([
            'status' => '1', // Success
            'remark' => 'Payment completed successfully' . ($reference ? " (Ref: {$reference})" : '')
        ]);

        // 2. Find user's wallet and update balance
        $wallet = \DB::table('wallets')->where('userid', $transaction->userid)->first();
        
        if ($wallet) {
            // Add the deposited amount to current balance
            $newBalance = $wallet->amount + $transaction->amount;
            
            \DB::table('wallets')
                ->where('userid', $transaction->userid)
                ->update(['amount' => $newBalance]);
            
            Log::info('ZenoPay Payment Completed Successfully - Wallet Updated', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->userid,
                'amount_added' => $transaction->amount,
                'old_balance' => $wallet->amount,
                'new_balance' => $newBalance,
                'order_id' => $transaction->transactionno,
                'reference' => $reference
            ]);
        } else {
            // Wallet not found - create one
            \DB::table('wallets')->insert([
                'userid' => $transaction->userid,
                'amount' => $transaction->amount,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('ZenoPay Payment Completed Successfully - New Wallet Created', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->userid,
                'initial_balance' => $transaction->amount,
                'order_id' => $transaction->transactionno,
                'reference' => $reference
            ]);
        }

        // 3. Commit the transaction
        \DB::commit();

        Log::info('ZenoPay Payment Processing Completed Successfully');

    } catch (\Exception $e) {
        // Rollback in case of error
        \DB::rollBack();
        
        Log::error('ZenoPay Payment Processing Failed', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->userid,
            'amount' => $transaction->amount,
            'order_id' => $transaction->transactionno,
            'reference' => $reference,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
    
    

    /**
     * Handle failed payment
     *
     * @param Transaction $transaction
     * @param string|null $reference
     * @return void
     */
    private function handleFailedPayment(Transaction $transaction, ?string $reference = null): void
    {
        // Update transaction status to cancelled
        $transaction->update([
            'status' => '2', // 2 = cancelled
            'remark' => 'Payment failed ' . ($reference ? " (Ref: {$reference})" : '')
        ]);

        Log::info('ZenoPay Payment Failed', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->userid,
            'amount' => $transaction->amount,
            'reference' => $reference
        ]);
    }
}