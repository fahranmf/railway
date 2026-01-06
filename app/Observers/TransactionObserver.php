<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\ProductBatch;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    /**
     * Handle the Transaction "updated" event.
     * Dijalankan otomatis saat data transaksi di-edit/disimpan.
     */
    public function updated(Transaction $transaction): void
    {
        // 1. Cek: Apakah status berubah jadi 'paid'?
        if ($transaction->isDirty('status') && $transaction->status === 'paid') {

            // --- TAHAP 1: VALIDASI TOTAL STOK DULU ---
            // Kita cek dulu semua barang belanjaan, cukup gak total stoknya?
            // Jangan sampai udah dipotong setengah, eh barang lain stoknya kurang.
            foreach ($transaction->items as $item) {
                $totalStockAvailable = ProductBatch::where('product_id', $item->product_id)
                    ->sum('stock');

                if ($totalStockAvailable < $item->qty) {
                    // Kalau stok kurang, batalkan transaksi
                    $transaction->updateQuietly(['status' => 'pending']);

                    Notification::make()
                        ->title('Stok Tidak Cukup!')
                        ->body("Produk {$item->product->name} total stoknya kurang. Transaksi dibatalkan.")
                        ->danger()
                        ->send();

                    return; // Stop proses, jangan lanjut potong stok
                }
            }

            // --- TAHAP 2: EKSEKUSI POTONG STOK (ALGORITMA FEFO) ---
            // Gunakan DB Transaction agar kalau ada error di tengah, semua perubahan dibatalkan
            DB::transaction(function () use ($transaction) {

                foreach ($transaction->items as $item) {
                    $qtyNeeded = $item->qty; // Jumlah yang harus diambil

                    // AMBIL BATCH: Urutkan dari Expired Date TERDEKAT (Ascending)
                    // Hanya ambil batch yang stoknya masih ada (> 0)
                    $batches = ProductBatch::where('product_id', $item->product_id)
                        ->where('stock', '>', 0)
                        ->orderBy('expired_date', 'asc') // <--- INI KUNCI FEFO
                        ->get();

                    foreach ($batches as $batch) {
                        // Jika kebutuhan sudah terpenuhi, berhenti cari batch lain
                        if ($qtyNeeded <= 0) break;

                        // Cek stok di batch ini
                        if ($batch->stock >= $qtyNeeded) {
                            // KASUS A: Stok di batch ini CUKUP untuk memenuhi semua kebutuhan
                            $batch->decrement('stock', $qtyNeeded);
                            $qtyNeeded = 0; // Kebutuhan selesai
                        } else {
                            // KASUS B: Stok di batch ini ADA TAPI KURANG (Misal butuh 10, ada 4)
                            // Ambil semua yang ada di batch ini
                            $stockInBatch = $batch->stock;
                            $batch->update(['stock' => 0]); // Habiskan stok batch ini

                            // Kurangi sisa kebutuhan
                            $qtyNeeded -= $stockInBatch;
                        }
                    }
                }
            });

            // Kirim Notifikasi Sukses
            Notification::make()
                ->title('Transaksi Berhasil (FEFO)')
                ->body('Stok obat telah dikurangi otomatis berdasarkan tanggal kadaluarsa terdekat.')
                ->success()
                ->send();
        }
    }
}
