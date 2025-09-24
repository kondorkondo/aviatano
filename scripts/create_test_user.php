<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // User details
    $name = 'Test User';
    $email = 'test@example.com';
    $mobile = '0746341778';
    $password = 'password123';
    $country = 'TZ';
    $currency = 'TSh';
    $gender = 'male';
    $isadmin = '0'; // Regular user, not admin

    // Check if user already exists
    $existingUser = User::where('email', $email)->orWhere('mobile', $mobile)->first();
    
    if ($existingUser) {
        echo "❌ User already exists with email: {$email} or mobile: {$mobile}\n";
        echo "User ID: {$existingUser->id}\n";
        echo "Name: {$existingUser->name}\n";
        echo "Email: {$existingUser->email}\n";
        echo "Mobile: {$existingUser->mobile}\n";
        echo "Country: {$existingUser->country}\n";
        echo "Currency: {$existingUser->currency}\n";
        exit;
    }

    // Create new user
    $user = new User;
    $user->name = $name;
    $user->email = $email;
    $user->mobile = $mobile;
    $user->promocode = null;
    $user->country = $country;
    $user->gender = $gender;
    $user->currency = $currency;
    $user->isadmin = $isadmin;
    $user->password = Hash::make($password);
    
    if ($user->save()) {
        echo "✅ Test user created successfully!\n";
        echo "User ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Mobile: {$user->mobile}\n";
        echo "Country: {$user->country}\n";
        echo "Currency: {$user->currency}\n";
        echo "Password: {$password}\n";
        echo "Admin: " . ($user->isadmin ? 'Yes' : 'No') . "\n";
        echo "\n";
        echo " Login URL: http://127.0.0.1:8000/login\n";
        echo " Use this mobile number for ZenoPay testing: {$mobile}\n";
    } else {
        echo " Failed to create user\n";
    }

} catch (Exception $e) {
    echo " Error: " . $e->getMessage() . "\n";
}

