<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransactionItem;
use App\Models\ProductBatch;

class AssignBatchesToTransactions extends Command
{
    /**
     * Nama dan deskripsi command
     */
    protected $signature = 'transactions:assign-batches {--force : Skip confirmation}';
    protected $description = 'Assign FEFO batches to transaction items that don\'t have batch records';

    /**
     * Execute command
     */
    public function handle()
    {
        $this->info('🔍 Scanning transaction items without batches...');

        // Cari semua transaction_items yang belum punya batch (product_batch_id = null)
        $itemsWithoutBatch = TransactionItem::whereNull('product_batch_id')->get();

        if ($itemsWithoutBatch->isEmpty()) {
            $this->info('✅ Semua transaksi sudah memiliki batch. Tidak ada yang perlu diupdate.');
            return 0;
        }

        $this->warn("⚠️  Ditemukan {$itemsWithoutBatch->count()} items tanpa batch.");

        if (!$this->option('force')) {
            if (!$this->confirm('Lanjutkan untuk assign batches FEFO?')) {
                $this->info('Cancelled.');
                return 1;
            }
        }

        $assigned = 0;
        $failed = 0;

        foreach ($itemsWithoutBatch as $item) {
            try {
                // Cari batch FEFO (expired_date paling awal) untuk produk ini
                $batch = ProductBatch::where('product_id', $item->product_id)
                    ->where('stock', '>', 0)  // Pastikan masih ada stok
                    ->orderBy('expired_date', 'asc')  // FEFO: terlama dulu
                    ->first();

                if ($batch) {
                    $item->update(['product_batch_id' => $batch->id]);
                    $this->line("✓ Transaction Item #{$item->id}: Assigned Batch #{$batch->id} ({$batch->batch_number})");
                    $assigned++;
                } else {
                    $this->warn("✗ Transaction Item #{$item->id} (Product #{$item->product_id}): No available batch found");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Transaction Item #{$item->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("   ✅ Assigned: $assigned");
        $this->error("   ❌ Failed: $failed");

        return 0;
    }
}
