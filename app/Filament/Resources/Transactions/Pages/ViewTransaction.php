<?php
namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function resolveRecord(int|string $key): \Illuminate\Database\Eloquent\Model
    {
        // Eager load items, batch, and product for detail view
        $record = static::getResource()::getEloquentQuery()
            ->with([
                'items.batch.product',
                'items.product',
                'user'
            ])
            ->findOrFail($key);
        
        // Pastikan relasi ter-load dengan memicu akses
        $record->load('items.batch', 'items.product');
        
        return $record;
    }
}

