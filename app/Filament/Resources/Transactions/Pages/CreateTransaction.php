<?php

namespace App\Filament\Resources\Transactions\Pages; // <--- SAYA UBAH INI (Sesuai nama folder Anda)

// Pastikan import Resource ini mengarah ke file TransactionResource.php yang benar.
// Jika file TransactionResource.php ada di folder 'App/Filament/Resources/Transactions', gunakan baris ini:
use App\Filament\Resources\Transactions\TransactionResource;
// TAPI, jika file TransactionResource.php ada di folder 'App/Filament/Resources', gunakan:
// use App\Filament\Resources\TransactionResource;

use App\Models\Product;
use App\Models\ProductBatch;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['items']) && is_array($data['items'])) {
            $total = collect($data['items'])->reduce(function ($carry, $item) {
                $qty = intval($item['quantity'] ?? 0);
                $price = intval($item['price'] ?? 0);
                return $carry + ($qty * $price);
            }, 0);

            $data['total_amount'] = $total;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;

        foreach ($transaction->items as $item) {

            // A. Potong Stok Batch
            if ($item->product_batch_id) {
                $batch = ProductBatch::find($item->product_batch_id);
                if ($batch) {
                    $batch->stock = $batch->stock - $item->quantity;
                    $batch->save();
                }
            }

            // B. Potong Stok Global (Opsional)
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock = $product->stock - $item->quantity;
                $product->save();
            }
        }

        Notification::make()
            ->title('Transaksi Sukses')
            ->body('Data disimpan & stok obat berhasil dikurangi.')
            ->success()
            ->send();
    }
}
