<?php

require 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check transaction items without batch
$count = \App\Models\TransactionItem::whereNull('product_batch_id')->count();
echo "Items tanpa batch: " . $count . "\n\n";

// Check transaction #3 specifically
$transaction = \App\Models\Transaction::with('items.batch.product', 'user')->find(3);

if ($transaction) {
    echo "=== Transaction #3 Details ===\n";
    echo "Invoice: " . $transaction->invoice_code . "\n";
    echo "Total Amount: " . $transaction->total_amount . "\n\n";

    echo "Items:\n";
    foreach ($transaction->items as $item) {
        $batchInfo = $item->batch ?
            "Batch: {$item->batch->batch_number}, Exp: {$item->batch->expired_date}" :
            "Batch: NULL";
        echo "- ID: {$item->id}, Product ID: {$item->product_id}, Product Batch ID: {$item->product_batch_id}, {$batchInfo}\n";
    }
} else {
    echo "Transaction #3 not found\n";
}
