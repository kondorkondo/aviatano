<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Setting;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Define default settings
    $defaultSettings = [
        'min_recharge' => '1000',
        'max_recharge' => '100000',
        'min_withdraw' => '500',
        'max_withdraw' => '50000',
        'site_name' => 'Aviator Game',
        'site_currency' => 'TSh',
        'site_country' => 'TZ',
        'game_status' => '1',
        'maintenance_mode' => '0'
    ];

    echo "🔧 Creating default settings...\n\n";

    foreach ($defaultSettings as $category => $value) {
        // Check if setting already exists
        $existingSetting = Setting::where('category', $category)->first();
        
        if ($existingSetting) {
            echo "✅ Setting '{$category}' already exists with value: {$existingSetting->value}\n";
        } else {
            // Create new setting
            $setting = new Setting();
            $setting->category = $category;
            $setting->value = $value;
            $setting->status = '1'; // Active status
            $setting->save();
            
            echo "✅ Created setting '{$category}' with value: {$value}\n";
        }
    }

    echo "\n🎉 Settings setup completed!\n";
    echo "💡 The deposit page should now work without errors.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
