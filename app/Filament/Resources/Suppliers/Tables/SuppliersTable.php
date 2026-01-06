<?php
namespace App\Filament\Resources\Suppliers\Tables;
use Filament\Tables;

class SuppliersTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
            Tables\Columns\TextColumn::make('contact_person'),
            Tables\Columns\TextColumn::make('phone')->icon('heroicon-m-phone'),
            Tables\Columns\TextColumn::make('address')->limit(30)->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
