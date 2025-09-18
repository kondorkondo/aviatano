<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Update existing users to use TSh currency and TZ country
User::where('currency', '₹')->orWhere('country', 'IN')->update([
    'currency' => 'TSh',
    'country' => 'TZ'
]);

echo "Currency updated to TSh for existing users\n";

