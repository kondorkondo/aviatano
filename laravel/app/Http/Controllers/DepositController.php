<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Deposit; // Assuming you have a Deposit model

class DepositController extends Controller
{
    public function depositNow(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'mobile_no' => 'required',
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $orderId = Str::uuid()->toString();

        try {
            $response = Http::withHeaders([
                'x-api-key' => env('ZENO_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://zenoapi.com/api/payments/mobile_money_tanzania', [
                "order_id"    => $orderId,
                "buyer_email" => $request->email,
                "buyer_name"  => $request->name,
                "buyer_phone" => $request->mobile_no,
                "amount"      => (int) $request->amount,
                "webhook_url" => route('payment.webhook'),
            ]);

            $data = $response->json();

            // Save transaction to DB (pending)
            Deposit::create([
                'user_id'       => auth()->id(),
                'order_id'      => $orderId,
                'amount'        => $request->amount,
                'status'        => 'PENDING',
                'buyer_email'   => $request->email,
                'buyer_name'    => $request->name,
                'buyer_phone'   => $request->mobile_no,
            ]);

            if ($data['status'] === 'success') {
                return redirect()->back()->with('msg', 'Success');
            } else {
                return redirect()->back()->with('error', $data['message'] ?? 'Something went wrong');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Deposit failed: ' . $e->getMessage());
        }
    }

    public function paymentWebhook(Request $request)
    {
        // Verify API key in headers
        if ($request->header('x-api-key') !== env('ZENO_API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        if (isset($payload['order_id']) && $payload['payment_status'] === 'COMPLETED') {
            $deposit = Deposit::where('order_id', $payload['order_id'])->first();

            if ($deposit) {
                $deposit->status = 'COMPLETED';
                $deposit->reference = $payload['reference'] ?? null;
                $deposit->save();

                // TODO: Credit user balance here
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
