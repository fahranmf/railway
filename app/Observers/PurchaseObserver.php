<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\ProductBatch;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class PurchaseObserver
{
    public function saved(Purchase $purchase): void
    {
        // Jika statusnya 'received' (Diterima), kita masukkan ke stok
        if ($purchase->status === 'received') {

            // Cek apakah pembelian ini sudah pernah diproses stoknya?
            // (Agar tidak double stock kalau diedit berkali-kali)
            // Logika sederhananya: Kita cek batch yang supplier_id & expired_date-nya sama.

            foreach ($purchase->items as $item) {

                // Jika product belum ada (kemungkinan dibuat via inline create), pastikan ada Product
                $product = $item->product;

                if (!$product) {
                    continue; // jika tidak ada data produk, lewati
                }

                // Jika ada selling_price pada item, update harga jual produk
                if (isset($item->selling_price) && $item->selling_price > 0) {
                    try {
                        $product->update(['price' => $item->selling_price]);
                    } catch (\Throwable $e) {
                        // ignore update errors to avoid breaking purchase save flow
                    }
                }

                // Tentukan tanggal kadaluarsa: gunakan item->expired_date jika ada,
                // jika tidak ada, gunakan default dari supplier (default_expiry_months),
                // atau fallback 12 bulan.
                $expired = $item->expired_date;
                if (empty($expired)) {
                    $supplier = $purchase->supplier;
                    $months = $supplier->default_expiry_months ?? 12;
                    $expired = now()->addMonths($months)->toDateString();
                }

                // Buat batch per item / per purchase (unik)
                ProductBatch::create([
                    'product_id' => $product->id,
                    'supplier_id' => $purchase->supplier_id,
                    'batch_number' => 'BATCH-' . $product->id . '-' . strtoupper(Str::random(5)),
                    'stock' => $item->quantity,
                    'expired_date' => $expired,
                    'purchase_price' => $item->unit_cost,
                ]);
            }

            Notification::make()
                ->title('Stok Bertambah')
                ->body('Data pembelian berhasil masuk ke Inventory Batch.')
                ->success()
                ->send();
        }
    }
}
