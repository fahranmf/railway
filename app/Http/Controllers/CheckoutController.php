<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductBatch;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    // MENERIMA REQUEST DARI FORM KERANJANG
    public function process(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'payment_method' => 'required|in:cash,midtrans',
        ]);

        $carts = Cart::with('product')->where('user_id', $user->id)->get();
        if ($carts->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang kosong');
        }

        $totalPrice = $carts->sum(fn($c) => $c->product->price * $c->quantity);

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'total_amount' => $totalPrice,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'paid' : 'pending',
            ]);

            // 🔥 CASH → LANGSUNG POTONG STOK
            if ($request->payment_method === 'cash') {
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

                DB::commit();
                return redirect()
                    ->route('transactions.show', $transaction->id)
                    ->with('success', 'Pembayaran tunai berhasil.');
            }

            // 💳 MIDTRANS → JANGAN POTONG STOK
            foreach ($carts as $cart) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cart->product_id, // ✅ WAJIB
                    'product_batch_id' => null,         // belum tahu batch
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);
            }


            DB::commit();
            return redirect()->route('midtrans.pay', $transaction->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

}
