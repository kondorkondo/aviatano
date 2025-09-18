<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Wallet;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $userId = 2; // Test user ID
    
    // Check if wallet already exists
    $existingWallet = Wallet::where('userid', $userId)->first();
    
    if ($existingWallet) {
        echo " Wallet already exists for user ID: {$userId}\n";
        echo "Current balance: {$existingWallet->amount} TSh\n";
        exit;
    }

    // Create new wallet
    $wallet = new Wallet;
    $wallet->userid = $userId;
    $wallet->amount = 0.00; // Start with zero balance
    $wallet->save();

    echo "✅ Wallet created successfully for user ID: {$userId}\n";
    echo "Initial balance: {$wallet->amount} TSh\n";
    echo "\n";
    echo " The user can now test deposits using the ZenoPay mobile money integration!\n";

} catch (Exception $e) {
    echo " Error: " . $e->getMessage() . "\n";
}
