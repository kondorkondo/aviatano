<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$email = $argv[1] ?? 'admin@example.com';
$password = $argv[2] ?? 'Admin@12345';
$name = $argv[3] ?? 'Admin';
$mobile = $argv[4] ?? '9999999999';

$user = User::where('email', $email)->first();
if (!$user) {
	$user = new User();
	$user->email = $email;
}

$user->name = $name;
$user->mobile = $mobile;
$user->promocode = null;
$user->country = 'TZ';
$user->gender = 'male';
$user->currency = 'TSh';
$user->isadmin = '1';
$user->password = Hash::make($password);
$user->save();

echo "Admin user ready: {$email}\n";
