<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables;

class UsersTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama User')
                ->searchable()
                ->sortable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->icon('heroicon-m-envelope')
                ->copyable(),

            Tables\Columns\TextColumn::make('role')
                ->label('Role')
                ->badge() // Badge warna otomatis dari Enum
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Terdaftar Sejak')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
