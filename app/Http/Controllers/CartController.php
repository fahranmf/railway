<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductBatch; // <-- PENTING: Wajib import ini untuk cek expired
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Menampilkan halaman keranjang
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        // Hitung total untuk kemudahan di view
        $total = 0;
        foreach($carts as $c) {
            $total += $c->product->price * $c->quantity;
        }

        return view('frontend.cart', compact('carts', 'total'));
    }

    /**
     * TAMBAH KE KERANJANG DENGAN VALIDASI KETAT
     */
    public function addToCart(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        // ====================================================
        // 1. VALIDASI STOK NYATA (Stok Real vs Expired)
        // ====================================================
        // Kita hitung stok yang VALID saja (Stok ada DAN Belum Expired)
        $validStock = ProductBatch::where('product_id', $id)
                        ->where('stock', '>', 0)
                        ->whereDate('expired_date', '>', now()) // Filter: Hanya yang belum expired
                        ->sum('stock');

        // Jika stok valid 0 (Entah habis beneran, atau sisa stok cuma barang expired)
        if ($validStock <= 0) {
            return redirect(url()->previous() . '#product-' . $id)
                   ->with('error', 'Maaf, stok obat ini habis atau sudah kadaluarsa (Expired).');
        }

        // ====================================================
        // 2. CEK KERANJANG EXISTING
        // ====================================================
        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_id', $id)
                            ->first();

        if ($existingCart) {
            // Cek apakah penambahan +1 akan melebihi stok VALID
            if ($existingCart->quantity + 1 > $validStock) {
                 return redirect(url()->previous() . '#product-' . $id)
                        ->with('error', 'Stok tersedia hanya ' . $validStock . ' (Sisanya sudah Expired).');
            }

            $existingCart->increment('quantity');
        } else {
            // Jika belum ada, buat baru
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $id,
                'quantity' => 1
            ]);
        }

        return redirect(url()->previous() . '#product-' . $id)->with('success', 'Berhasil masuk keranjang!');
    }

    /**
     * UPDATE JUMLAH ITEM (Juga divalidasi)
     */
    public function update(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $qty = (int) $request->quantity;

        // Validasi minimal 1
        if ($qty < 1) {
            return back()->with('error', 'Jumlah minimal 1');
        }

        // ====================================================
        // VALIDASI STOK SAAT UPDATE
        // ====================================================
        // Hitung ulang stok valid saat user ganti angka
        $validStock = ProductBatch::where('product_id', $cart->product_id)
                        ->where('stock', '>', 0)
                        ->whereDate('expired_date', '>', now())
                        ->sum('stock');

        // Jika user minta lebih dari stok yang valid
        if ($qty > $validStock) {
            return back()->with('error', 'Maksimal pembelian adalah ' . $validStock . ' item (Stok lain expired).');
        }

        $cart->update(['quantity' => $qty]);
        return back()->with('success', 'Jumlah berhasil diubah');
    }

    // Hapus item
    public function destroy($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    // ==========================================
    // FUNGSI CHECKOUT SEDERHANA
    // Note: Sebaiknya gunakan CheckoutController yang kita buat sebelumnya
    // agar support FEFO dan pencatatan Batch ID.
    // Tapi jika Anda ingin pakai yang di sini, ini kodenya:
    // ==========================================
    public function checkout()
    {
        // Redirect ke CheckoutController yang lebih canggih (Recommended)
        // return redirect()->action([CheckoutController::class, 'process']);

        // ATAU Tetap pakai logic sederhana di sini (Not Recommended untuk tracking batch):

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang Anda kosong.');
        }

        try {
            DB::beginTransaction();

            // Hitung Total Bayar
            $totalPrice = 0;
            foreach ($carts as $cart) {
                $totalPrice += $cart->product->price * $cart->quantity;
            }

            // Buat Transaksi
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalPrice, // Sesuaikan dengan kolom DB baru (total_amount)
                'status' => 'paid',
                'created_at' => now(),
            ]);

            // Pindahkan Keranjang ke Item
            foreach ($carts as $cart) {
                // Warning: Metode sederhana ini tidak mencatat Batch ID (Expired tracking lemah)
                // Gunakan CheckoutController untuk hasil terbaik.
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,     // Gunakan 'quantity'
                    'price' => $cart->product->price,
                ]);
            }

            // Kosongkan Keranjang
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                             ->with('success', 'Pembayaran Berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
