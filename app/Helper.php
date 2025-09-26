<?php

use App\Models\Gameresult;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Userbit;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

function imageupload($file, $name, $path)
{
    $file_name = "";
    $file_type = "";
    $filePath = "";
    $size = "";

    if ($file) {
        $file_name = $file->getClientOriginalName();
        $file_type = $file->getClientOriginalExtension();
        $fileName = $name . "." . $file_type;
        Storage::disk('public')->put($path . $fileName, File::get($file));
        $filePath = "/" . 'storage/' . $path . $fileName;
    }
    return $file = [
        'fileName' => $file_name,
        'fileType' => $file_type,
        'filePath' => $filePath,
    ];
}

function datealgebra($date, $operator, $value, $format = "Y-m-d")
{
    if ($operator == "-") {
        $date = date_create($date);
        date_sub($date, date_interval_create_from_date_string($value));
        return date_format($date, $format);
    } elseif ($operator == "+") {
        $date = date_create($date);
        date_add($date, date_interval_create_from_date_string($value));
        return date_format($date, $format);
    }
}

function user($parameter,$id=null)
{
    if ($id == null) {
        return session()->get('userlogin')[$parameter];
    }else{
        $data = User::where('id', $id)->first();
        return $data->{$parameter};
    }
    // return session()->get('userlogin')[$parameter];
}

function userdetail($id, $parameter)
{
    $data = User::where('id', $id)->first();
    //return $data->{$parameter};
}

function admin($parameter)
{
    return session()->get('adminlogin')[$parameter];
}

function wallet($userid, $type = "string")
{
    $amount = Wallet::where('userid', $userid)->first();
    if ($amount && $amount->amount > 0) {
        if ($type == "num") {
            return $amount->amount;
        } else {
            return number_format($amount->amount);
        }
    } else {
        return 0;
    }
}

function setting($parameter)
{
    $setting = Setting::where('category', $parameter)->first();
    return $setting ? $setting->value : null;
}

function currentid()
{
    $data = Gameresult::orderBy('id', 'desc')->first();
    if ($data) {
        return $data->id;
    } else {
        return 0;
    }
}

function dformat($date, $format)
{
    $strd = date_create($date);
    // if (date($format) == date_format($strd, $format)) {
    //     return "Today";
    // }
    return date_format($strd, $format);
}

function resultbyid($id)
{
    $data = Gameresult::where('id', $id)->first();
    if ($data && $data->result != 'pending' && $data->result != '') {
        return $data->result;
    }
    return 0;
}

function userbetdetail($id,$parameter)
{
    $data = Userbit::where('id', $id)->first();
    if ($data) {
        return $data->{$parameter};
    }
    return 0;
}

// In your helpers.php or wherever addwallet is defined
function addwallet($userid, $amount, $operation = "+") {
    try {
        $wallet = \DB::table('wallets')->where('userid', $userid)->first();
        
        if ($operation == "+") {
            $newBalance = $wallet->amount + $amount;
        } else {
            $newBalance = $wallet->amount - $amount;
        }
        
        $updated = \DB::table('wallets')
            ->where('userid', $userid)
            ->update(['amount' => $newBalance]);
            
        return $updated > 0; // Return true if updated
        
    } catch (\Exception $e) {
        \Log::error('addwallet function failed', ['error' => $e->getMessage()]);
        return false;
    }
}

function appvalidate($input)
{
    if ($input == '' || $input == null || $input == 0) {
        return 'Not found!';
    } else {
        return $input;
    }
}

function lastrecharge($id, $parameter)
{
    $data = Transaction::where('userid', $id)->where('type', 'credit')->where('category', 'recharge')->orderBy('id', 'desc')->first();
    if ($data) {
        return $data->{$parameter};
    }
    return false;
}

function status($code, $type)
{
    if ($type == 'recharge') {
        if ($code == 0) {
            return array('color' => 'warning', 'name' => 'Pending');
        }
        if ($code == 1) {
            return array('color' => 'success', 'name' => 'Approved');
        }
        if ($code == 2) {
            return array('color' => 'danger', 'name' => 'Cancel');
        }
    } elseif ($type == "user") {
        if ($code == 0) {
            return array('color' => 'danger', 'name' => 'Inactive');
        }
        if ($code == 1) {
            return array('color' => 'success', 'name' => 'Active');
        }
        if ($code == 2) {
            return array('color' => 'warning', 'name' => 'Pending');
        }
    }
}

function platform($id)
{
    if ($id == 2) {
        return 'phonepay';
    } elseif ($id == 3) {
        return 'upi';
    } elseif ($id == 1) {
        return 'gpay';
    } elseif ($id == 9) {
        return 'imps';
    } elseif ($id == 6) {
        return 'netbanking';
    } elseif ($id == 10) {
        return 'mobile_money_tanzania';
    } else {
        return 'other';
    }
}

function addtransaction($userid, $platform, $transactionno, $type, $amount, $category, $remark, $status)
{
    $trn = new Transaction;
    $trn->userid = $userid;
    $trn->platform = $platform;
    $trn->transactionno = $transactionno;
    $trn->type = $type;
    $trn->amount = $amount;
    $trn->category = $category;
    $trn->remark = $remark;
    $trn->status = $status;
    if ($trn->save()) {
        return true;
    }
    return false;
}

// ========== NEW FUNCTIONS ADDED FOR ZENOPAY WEBHOOK PROCESSING ==========

/**
 * Process ZenoPay successful payment and credit wallet
 * This function will be called from ZenoPayService webhook
 */
function processZenoPaySuccess($orderId, $reference = null)
{
    try {
        // Find the transaction by ZenoPay order_id
        $transaction = Transaction::where('transactionno', $orderId)->first();
        
        if (!$transaction) {
            Log::error('ZenoPay transaction not found', ['order_id' => $orderId]);
            return false;
        }
        
        // Check if transaction is already processed
        if ($transaction->status == '1') {
            Log::info('ZenoPay transaction already processed', ['order_id' => $orderId]);
            return true;
        }
        
        // Update transaction status to approved
        $transaction->update([
            'status' => '1', // 1 = approved
            'remark' => 'Payment completed via ZenoPay' . ($reference ? " (Ref: {$reference})" : ''),
            'updated_at' => now()
        ]);
        
        Log::info('ZenoPay transaction status updated', [
            'order_id' => $orderId,
            'user_id' => $transaction->userid,
            'amount' => $transaction->amount
        ]);
        
        // Credit user wallet using existing addwallet function
        $walletUpdated = addwallet($transaction->userid, $transaction->amount, '+');
        
        if ($walletUpdated !== false) {
            Log::info('ZenoPay wallet credit successful', [
                'order_id' => $orderId,
                'user_id' => $transaction->userid,
                'amount' => $transaction->amount,
                'new_balance' => $walletUpdated,
                'reference' => $reference
            ]);
            return true;
        } else {
            Log::error('ZenoPay wallet credit failed', [
                'order_id' => $orderId,
                'user_id' => $transaction->userid,
                'amount' => $transaction->amount
            ]);
            return false;
        }
        
    } catch (\Exception $e) {
        Log::error('ZenoPay processing exception', [
            'order_id' => $orderId,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

/**
 * Get transaction by ZenoPay order ID
 */
function getTransactionByZenoOrderId($orderId)
{
    return Transaction::where('transactionno', $orderId)->first();
}

/**
 * Check if wallet update is needed for ZenoPay transaction
 */
function needsWalletUpdate($orderId)
{
    $transaction = getTransactionByZenoOrderId($orderId);
    return $transaction && $transaction->status == '0'; // Only update if status is pending (0)
}






/**
 * Simple test function to verify ZenoPay integration
 */
function testZenoPayWalletUpdate($userId, $amount)
{
    try {
        $result = addwallet($userId, $amount, '+');
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Wallet update test successful',
                'new_balance' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Wallet update test failed'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Test error: ' . $e->getMessage()
        ];
    }
}