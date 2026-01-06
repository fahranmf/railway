<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Models\ProductBatch;
use Illuminate\Support\Str;

class PurchaseItemObserver
{
    public function created(PurchaseItem $item): void
    {
        $this->ensureBatchForItem($item);
    }

    public function updated(PurchaseItem $item): void
    {
        $this->ensureBatchForItem($item);
    }

    protected function ensureBatchForItem(PurchaseItem $item): void
    {
        $purchase = $item->purchase;

        if (! $purchase) {
            return;
        }

        // Hanya buat batch jika pembelian sudah berstatus 'received'
        if ($purchase->status !== 'received') {
            return;
        }

        $product = $item->product;
        if (! $product) {
            return;
        }

        // Tentukan tanggal kadaluarsa
        $expired = $item->expired_date;
        if (empty($expired)) {
            $supplier = $purchase->supplier;
            $months = $supplier->default_expiry_months ?? 12;
            $expired = now()->addMonths($months)->toDateString();
        } else {
            $expired = $item->expired_date->toDateString();
        }

        // Cek apakah batch serupa sudah ada (hindari duplikasi)
        $exists = ProductBatch::where('product_id', $product->id)
            ->where('supplier_id', $purchase->supplier_id)
            ->where('expired_date', $expired)
            ->where('purchase_price', $item->unit_cost)
            ->exists();

        if ($exists) {
            return;
        }

        ProductBatch::create([
            'product_id' => $product->id,
            'supplier_id' => $purchase->supplier_id,
            'batch_number' => 'BATCH-' . $product->id . '-' . strtoupper(Str::random(5)),
            'stock' => $item->quantity,
            'expired_date' => $expired,
            'purchase_price' => $item->unit_cost,
        ]);
    }
}
