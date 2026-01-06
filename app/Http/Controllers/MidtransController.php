<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\ProductBatch;
use App\Models\TransactionItem;

use Illuminate\Support\Facades\DB;


class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * HALAMAN PEMBAYARAN MIDTRANS
     */
    
    public function pay(Transaction $transaction)
    {
        abort_if($transaction->status !== 'pending', 403);

        // ✅ GENERATE ORDER ID SEKALI
        if (!$transaction->midtrans_order_id) {
            $orderId = 'TRX-' . $transaction->id . '-' . time();

            $transaction->update([
                'midtrans_order_id' => $orderId
            ]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->midtrans_order_id,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('midtrans.pay', compact('snapToken', 'transaction'));
    }


    /**
     * WEBHOOK MIDTRANS
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        // 🔐 Validasi signature
        $signatureKey = hash(
            'sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('services.midtrans.server_key')
        );

        if ($signatureKey !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where(
            'midtrans_order_id',
            $payload['order_id']
        )->firstOrFail();


        // ⛔️ ANTI DOUBLE WEBHOOK
        if ($transaction->status === 'paid') {
            return response()->json(['message' => 'Already processed']);
        }

        if (in_array($payload['transaction_status'], ['settlement', 'capture'])) {

            DB::transaction(function () use ($transaction) {

                $carts = Cart::with('product')
                    ->where('user_id', $transaction->user_id)
                    ->get();

                foreach ($carts as $cart) {
                    $product = $cart->product;
                    $qtyNeeded = $cart->quantity;

                    $threshold = now()->startOfDay()->addDays(7);

                    $batches = ProductBatch::where('product_id', $product->id)
                        ->where('stock', '>', 0)
                        ->where(function ($q) use ($threshold) {
                            $q->whereNull('expired_date')
                                ->orWhereDate('expired_date', '>=', $threshold);
                        })
                        ->orderBy('expired_date', 'asc')
                        ->lockForUpdate()
                        ->get();

                    foreach ($batches as $batch) {
                        if ($qtyNeeded <= 0)
                            break;

                        $take = min($batch->stock, $qtyNeeded);

                        $batch->decrement('stock', $take);
                        $qtyNeeded -= $take;

                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_batch_id' => $batch->id,
                            'quantity' => $take,
                            'price' => $product->price,
                        ]);
                    }

                    if ($qtyNeeded > 0) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi");
                    }
                }

                Cart::where('user_id', $transaction->user_id)->delete();
                $transaction->update(['status' => 'paid']);
            });

        } elseif (in_array($payload['transaction_status'], ['expire', 'cancel', 'deny'])) {

            $transaction->update(['status' => 'cancel']);
        }

        return response()->json(['message' => 'OK']);
    }


}
