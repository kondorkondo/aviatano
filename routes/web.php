<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\Gamesetting;
use App\Http\Controllers\Pages;
use App\Http\Controllers\Userdetail;
use App\Http\Controllers\Adminapi;
use App\Http\Controllers\ZenoPayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/storagelink', function () {
	$target = '/home/u558340823/domains/thixpro.in/public_html/aviator/laravel/storage/app/public/';
   $shortcut = '/home/u558340823/domains/thixpro.in/public_html/aviator/storage/';
   symlink($target, $shortcut);
    dd('storage link successfully');
});

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('optimize');
    dd('Cache cleared successfully');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
});

// Auth Login
Route::post('/auth/login', [Authentication::class, "login"]);
Route::post('/auth/register', [Authentication::class, "register"]);
Route::get('/is_login', [Userdetail::class, "is_login"]);
Route::get('/game-cron', [Gamesetting::class, "cronjob"]);

// Auth Admin Login
Route::post('/auth/admin/login', [Authentication::class, "adminlogin"]);

// Admin Login
Route::get('/admin', [Admin::class, "login"]);
Route::group(['prefix' => 'admin/', 'middleware' => ['isAdmin']], function () {
    Route::get('/dashboard', [Admin::class, "dashboard"]);
    Route::get('/user-list', [Admin::class, "userlist"]);
    Route::get('/change-password', [Admin::class, "chagepassword"]);
    Route::get('/user/edit/{id}', [Admin::class, "useredit"]);
    Route::get('/recharge-history', [Admin::class, "rechargehistory"]);
    Route::get('/withdrawal-history', [Admin::class, "withdrawalhistory"]);
    Route::get('/amount-setup/{id?}', [Admin::class, "amountsetup"]);
    Route::get('/bank-detail', [Admin::class, "bankdetail"]);
    
    Route::group(['prefix' => 'api/'], function () {
        Route::post('/changepassword', [Adminapi::class, "changepassword"]);
        Route::post('/edituser', [Adminapi::class, "edituser"]);
        Route::post('/recharge/{event}', [Adminapi::class, "rechargeapproval"]);
        Route::post('/withdraw/{event}', [Adminapi::class, "withdrawalapproval"]);
        Route::post('/user/delete', [Adminapi::class, "userdelete"]);
        Route::post('/editamountsetup', [Adminapi::class, "editamountsetup"]);
        Route::post('/bankdetail', [Adminapi::class, "editbankdetail"]);
        Route::post('/updatewallet', [Adminapi::class, "updatewallet"]);
    });

    Route::get('/logout', [Admin::class, "logout"]);
});

Route::group(['middleware' => ['isUser']], function () {
    Route::get('/profile', [Userdetail::class, "profile"]);
    Route::get('/crash', [Pages::class, "aviator"]);
    Route::get('/deposit', [Pages::class, 'deposit']);
    Route::get('/amount-transfer', [Pages::class, "amount_transfer"]);
    Route::get('/withdraw', function () {
        return view('withdraw');
    });
    Route::get('/referal', function () {
        return view('refferal');
    });
    Route::get('/level-management', [Pages::class,'level_management']);

    Route::get('/deposit_withdrawals', [Userdetail::class, "deposit_withdrawal"]);
    Route::get('/logout', function () {
        if (session()->has('userlogin')) {
            session()->forget('userlogin');
        }
        return redirect('/');
    });
    
    //Api
    Route::get('/get_user_details', [Userdetail::class, "get_user_detail"]);
    
    //Data api
    Route::post('/user/withdrawal_list', [Userdetail::class, "withdrawal_list"]);
    Route::post('/game/existence', [Gamesetting::class, "game_existence"]);
    Route::post('/game/crash_plane', [Gamesetting::class, "crash_plane"]);
    Route::post('/game/new_game_generated', [Gamesetting::class, "new_game_generated"]);
    Route::post('/game/increamentor', [Gamesetting::class, "increamentor"]);
    Route::post('/game/game_over', [Gamesetting::class, "game_over"]);
    Route::post('/game/add_bet', [Gamesetting::class, "betNow"]);
	Route::get('/cash_out', [Gamesetting::class, "cashout"]);
    Route::post('/game/currentlybet', [Gamesetting::class, "currentlybet"]);
    Route::post('/game/my_bets_history', [Gamesetting::class, "my_bets_history"]);
    Route::get('/payment_gateway_details', [Adminapi::class, "payment_gateway"]);
    Route::post('/insert/withdrawal', [Adminapi::class, "withdrawal_query"]);
    Route::post('/depositNow', [Adminapi::class, "depositNow"]);
    Route::post('/wallet_transfer', [Userdetail::class, "wallet_transfer"]);
});

// ==================== ZENOPAY ROUTES FIX ====================

// Remove duplicate webhook routes - KEEP ONLY THIS ONE:
Route::post('/api/zenopay/webhook', [ZenoPayController::class, 'webhook']);

// Test routes (keep these for debugging)
Route::get('/zenopay/test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Webhook endpoint is working!',
        'timestamp' => now(),
        'correct_webhook_url' => 'https://aviatano.site/api/zenopay/webhook'
    ]);
});

Route::get('/test-zenopay-fix/{orderId}', function($orderId) {
    $zenoPayService = app()->make(App\Services\ZenoPayService::class);
    $result = $zenoPayService->manualWalletUpdate($orderId);
    
    return response()->json($result);
});

Route::get('/test-webhook-bypass', function() {
    $webhookUrl = 'https://aviatano.site/api/zenopay/webhook?bypass_key=1';
    
    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->post($webhookUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-Key' => 'any_key_will_work_now' // This will be bypassed
            ],
            'json' => [
                'order_id' => 'zp_68d5e2b21a0445.82841389',
                'payment_status' => 'COMPLETED',
                'reference' => 'TEST_BYPASS_123'
            ],
            'timeout' => 10
        ]);
        
        return response()->json([
            'accessible' => true,
            'status_code' => $response->getStatusCode(),
            'response' => 'Webhook processed with bypass!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'accessible' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/check-api-key', function() {
    return response()->json([
        'api_key_config' => substr(config('zenopay.api_key'), 0, 10) . '...',
        'api_key_length' => strlen(config('zenopay.api_key')),
        'webhook_url' => config('zenopay.webhook_url')
    ]);
});

Route::get('/test-wallet-update/{orderId}', function($orderId) {
    try {
        // Manually process a webhook to test wallet update
        $zenoPayService = app()->make(App\Services\ZenoPayService::class);
        
        $webhookData = [
            'order_id' => $orderId,
            'payment_status' => 'COMPLETED',
            'reference' => 'MANUAL_TEST_456'
        ];
        
        $result = $zenoPayService->processWebhook($webhookData);
        
        // Check if wallet was updated
        $transaction = \DB::table('transactions')->where('transactionno', $orderId)->first();
        $wallet = \DB::table('wallets')->where('userid', $transaction->userid)->first();
        
        return response()->json([
            'webhook_processed' => $result,
            'transaction' => $transaction,
            'wallet' => $wallet,
            'message' => $result ? 'Wallet should be updated!' : 'Webhook processing failed'
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});



Route::get('/test-cashout-fix/{userId}/{betId}/{multiplier}', function($userId, $betId, $multiplier) {
    try {
        // Create a test bet first
        $testBet = \DB::table('userbits')->insert([
            'userid' => $userId,
            'amount' => 100,
            'type' => 0,
            'gameid' => currentid(),
            'section_no' => 0,
            'cashout_multiplier' => 0,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $betId = \DB::getPdo()->lastInsertId();
        
        // Test cashout
        $request = new \Illuminate\Http\Request([
            'game_id' => currentid(),
            'bet_id' => $betId,
            'win_multiplier' => $multiplier
        ]);
        
        $gameSetting = new \App\Http\Controllers\Gamesetting();
        $result = $gameSetting->cashout($request);
        
        return $result;
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});