<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Tables;

class CategoryTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Kategori')
                ->searchable()
                ->sortable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('slug')
                ->icon('heroicon-m-link')
                ->color('gray'),
        ];
    }
}
