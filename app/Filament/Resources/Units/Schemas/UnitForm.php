<?php
namespace App\Filament\Resources\Units\Schemas;

use Filament\Schemas;
use Filament\Forms;

class UnitForm
{
    public static function schema(): array
    {
        return [
            Schemas\Components\Section::make('Data Satuan')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Satuan')
                        ->placeholder('Contoh: Botol')
                        ->required(),
                    Forms\Components\TextInput::make('symbol')
                        ->label('Simbol / Singkatan')
                        ->placeholder('Contoh: Btl')
                        ->required(),
                ])->columns(2),
        ];
    }
}
