<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables;

class ProductTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('image')
                ->label('Foto')
                ->circular(),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->weight('bold')
                ->sortable(),

            Tables\Columns\TextColumn::make('sku')
                ->label('SKU')
                ->color('gray')
                ->searchable(),

            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori')
                ->sortable()
                ->badge()
                ->color('info'),

            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('IDR')
                ->sortable()
                ->weight('bold')
                ->color('success'),

            Tables\Columns\TextColumn::make('total_stock')
                ->label('Stok')
                ->sortable()
                ->badge()
                ->color(function ($state, $record) {
                    // Jika stok = 0 atau produk mendekati kedaluwarsa dalam 7 hari -> merah
                    if (isset($record->is_expiring_soon) && $record->is_expiring_soon) {
                        return 'danger';
                    }

                    return ($state > 0) ? 'success' : 'danger';
                }),

            Tables\Columns\IconColumn::make('is_active')
                ->label('Aktif')
                ->boolean(),
        ];
    }
}
