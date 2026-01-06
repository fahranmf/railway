<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch; // Pastikan Model ini benar sesuai file Anda
use Illuminate\Support\Facades\Log;

class ZeroStockExpired extends Command
{
    /**
     * Nama perintah untuk dipanggil di terminal
     */
    protected $signature = 'stock:cleanup';

    /**
     * Keterangan perintah
     */
    protected $description = 'Set stok menjadi 0 untuk obat yang sudah expired';

    /**
     * Eksekusi logika di sini
     */
    public function handle()
    {
        // Cari barang yang expired-nya KEMARIN atau sebelumnya ( < now() )
        // DAN stoknya masih ada ( > 0 )
        $affected = ProductBatch::where('expired_date', '<', now())
                                ->where('stock', '>', 0)
                                ->update(['stock' => 0]);

        if ($affected > 0) {
            $this->info("Berhasil: {$affected} batch obat expired telah di-nol-kan.");
            Log::info("SCHEDULER: {$affected} batch obat expired di-set ke 0 oleh sistem.");
        } else {
            $this->info("Tidak ada obat expired yang perlu dibersihkan hari ini.");
        }

        return 0;
    }
}
