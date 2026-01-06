<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * 1. Menampilkan Daftar Riwayat Transaksi (History)
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
                        ->with(['items.product', 'items.batch.product']) // Load lengkap
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('frontend.transactions.index', compact('transactions'));
    }

    /**
     * 2. Menampilkan Detail Struk (Halaman Sukses Bayar / Detail)
     */
    public function show(Transaction $transaction)
    {
        // KEAMANAN
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        // Load data super lengkap untuk menghindari "Produk Dihapus"
        $transaction->load([
            'user',
            'items.product',          // Ambil data produk induk (Backup)
            'items.batch.product'     // Ambil data produk via batch (Utama)
        ]);

        return view('frontend.transactions.show', compact('transaction'));
    }

    /**
     * 3. Halaman Khusus Print (Print View)
     */
    public function print(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak');
        }

        $transaction->load(['items.product', 'items.batch.product', 'user']);

        return view('transactions.print', compact('transaction'));
    }
}
