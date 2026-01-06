<?php
namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Schemas;
use Filament\Forms;

class SupplierForm
{
    public static function schema(): array
    {
        return [
            Schemas\Components\Section::make('Informasi Supplier')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Perusahaan')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('contact_person')->label('Nama Kontak'),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('phone')->tel(),
                    Forms\Components\Textarea::make('address')->columnSpanFull(),
                ])->columns(2),
        ];
    }
}
