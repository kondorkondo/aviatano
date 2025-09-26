<?php
// File: simple_debug_zenopay.php

echo "=== SIMPLE ZENOPAY DEBUG ===\n\n";

$apiKey = "your_zenopay_api_key"; // REPLACE WITH YOUR ACTUAL API KEY
$orderId = "zp_68d55acc323f49.42213864";
$userId = "50042";

// Step 1: Create the transaction if it doesn't exist
echo "1. Ensuring transaction exists...\n";
$transactionCheck = shell_exec("mysql -u username -p'password' dbname -e \"SELECT * FROM transactions WHERE transactionno = '{$orderId}';\" 2>/dev/null");

if (strpos($transactionCheck, $orderId) === false) {
    echo "   Creating transaction...\n";
    shell_exec("mysql -u username -p'password' dbname -e \"
        INSERT INTO transactions (userid, platform, transactionno, type, amount, category, remark, status, created_at, updated_at) 
        VALUES ('{$userId}', 'mobile_money_tanzania', '{$orderId}', 'credit', '1000', 'recharge', 'ZenoPay debug fix', '0', NOW(), NOW());
    \" 2>/dev/null");
    echo "   ✅ Transaction created\n";
} else {
    echo "   ✅ Transaction already exists\n";
}

// Step 2: Check current wallet balance
echo "\n2. Current wallet balance:\n";
$walletCheck = shell_exec("mysql -u username -p'password' dbname -e \"SELECT amount FROM wallets WHERE userid = '{$userId}';\" 2>/dev/null");
echo "   " . $walletCheck . "\n";

// Step 3: Test webhook with simple PHP curl
echo "3. Testing webhook with PHP curl...\n";

$url = "https://aviatano.site/api/zenopay/webhook";
$data = [
    "order_id" => $orderId,
    "payment_status" => "COMPLETED",
    "reference" => "PHP_TEST_" . time()
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-Key: " . $apiKey,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: " . $httpCode . "\n";
echo "   Response: " . $response . "\n";
if ($error) echo "   Error: " . $error . "\n";

// Step 4: Check if wallet updated
echo "\n4. Checking wallet after webhook...\n";
sleep(2);
$walletAfter = shell_exec("mysql -u username -p'password' dbname -e \"SELECT amount FROM wallets WHERE userid = '{$userId}';\" 2>/dev/null");
echo "   " . $walletAfter . "\n";

// Step 5: Check transaction status
echo "\n5. Transaction status after webhook:\n";
$txStatus = shell_exec("mysql -u username -p'password' dbname -e \"SELECT status, remark FROM transactions WHERE transactionno = '{$orderId}';\" 2>/dev/null");
echo "   " . $txStatus . "\n";

echo "\n=== DEBUG COMPLETE ===\n";
?>