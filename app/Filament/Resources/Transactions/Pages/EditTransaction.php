<?php
namespace App\Filament\Resources\Transactions\Pages;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hitung total_amount dari items jika belum ada
        if (isset($data['items']) && is_array($data['items'])) {
            $total = collect($data['items'])->reduce(fn ($carry, $item) =>
                $carry + (($item['qty'] ?? 0) * ($item['price'] ?? 0)),
            0);
            $data['total_amount'] = $total;
        }

        return $data;
    }
}
