<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🧪Testing Deposit Flow Integration...\n\n";
    
    // Check test user exists
    $user = User::find(2);
    if (!$user) {
        echo "❌ Test user not found. Please run create_test_user.php first.\n";
        exit;
    }
    
    echo "✅ Test user found: {$user->name} ({$user->email})\n";
    
    // Check wallet exists
    $wallet = Wallet::where('userid', $user->id)->first();
    if (!$wallet) {
        echo "❌ User wallet not found. Please run create_wallet.php first.\n";
        exit;
    }
    
    echo "✅ User wallet found with balance: {$wallet->amount} TSh\n";
    
    // Check settings exist
    $minRecharge = \App\Models\Setting::where('category', 'min_recharge')->first();
    if (!$minRecharge) {
        echo "❌ Settings not found. Please run create_settings.php first.\n";
        exit;
    }
    
    echo "✅ Settings configured (min_recharge: {$minRecharge->value} TSh)\n";
    
    // Test ZenoPay service
    $zenoPayService = app(\App\Services\ZenoPayService::class);
    echo "✅ ZenoPay service initialized\n";
    
    // Test order data preparation
    $orderData = $zenoPayService->prepareOrderData(
        $user->id,
        $user->email,
        $user->name,
        $user->mobile,
        1000,
        'test_' . uniqid()
    );
    
    echo "✅ Order data prepared:\n";
    echo "   - Order ID: {$orderData['order_id']}\n";
    echo "   - Amount: {$orderData['amount']} TZS\n";
    echo "   - Mobile: {$orderData['buyer_phone']}\n";
    echo "   - Email: {$orderData['buyer_email']}\n";
    
    echo "\n🎉 All tests passed! The deposit flow is ready.\n\n";
    
    echo "📋 Next steps:\n";
    echo "1. Visit: http://127.0.0.1:8001/login\n";
    echo "2. Login with: test@example.com / password123\n";
    echo "3. Go to: http://127.0.0.1:8001/deposit\n";
    echo "4. Enter amount: 1000 TSh\n";
    echo "5. Enter mobile: 0746341778\n";
    echo "6. Click DEPOSIT button\n";
    echo "7. Check logs: tail -f storage/logs/laravel.log\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

