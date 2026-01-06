<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::first();
        $products = Product::take(5)->get();

        if (! $supplier || $products->isEmpty()) {
            return;
        }

        // Create 3 sample purchases
        for ($i = 1; $i <= 3; $i++) {
            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'purchase_date' => now()->subDays(3 * $i),
                'reference_no' => 'INV' . now()->format('YmdHis') . Str::random(3),
                'status' => 'received',
                'user_id' => 1,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($products as $product) {
                $qty = rand(1, 5);
                $lastBatch = ProductBatch::where('product_id', $product->id)->latest('created_at')->first();

                $unitCost = $lastBatch?->purchase_price ?? rand(1000, 20000);
                $expired = $lastBatch?->expired_date ?? now()->addMonths(rand(6, 24))->toDateString();
                $selling = $product->price ?? ($unitCost * 2);

                $item = $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'selling_price' => $selling,
                    'expired_date' => $expired,
                ]);

                // Create batch record to reflect stocked items
                ProductBatch::create([
                    'product_id' => $product->id,
                    'supplier_id' => $supplier->id,
                    'batch_number' => 'SEED-' . strtoupper(Str::random(6)),
                    'stock' => $qty,
                    'expired_date' => $expired,
                    'purchase_price' => $unitCost,
                ]);

                $total += ($qty * $unitCost);
            }

            $purchase->update(['total_amount' => $total]);
        }
    }
}
