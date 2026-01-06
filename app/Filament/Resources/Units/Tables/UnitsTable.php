<?php
namespace App\Filament\Resources\Units\Tables;
use Filament\Tables;

class UnitsTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('symbol')->badge()->color('info'),
        ];
    }
}
