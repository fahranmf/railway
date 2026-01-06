<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas;
use Filament\Forms;

class ProductForm
{
    public static function schema(): array
    {
        return [
            Schemas\Components\Group::make()
                ->schema([
                    Schemas\Components\Section::make('Informasi Dasar')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Obat')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('sku')
                                ->label('SKU / Kode Barang')
                                ->default('OBT-' . strtoupper(uniqid())) // Dummy Auto Generate
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            Forms\Components\TextInput::make('price')
                                ->label('Harga Jual')
                                ->required()
                                ->numeric()
                                ->prefix('Rp'),
                        ])->columns(2),

                    Schemas\Components\Section::make('Detail Atribut')
                        ->schema([
                            // Relasi ke Kategori
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\TextInput::make('slug')->required(),
                                ]),

                            // Relasi ke Satuan
                            Forms\Components\Select::make('unit_id')
                                ->relationship('unit', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),
                        ])->columns(2),
                ])->columnSpan(2),

            Schemas\Components\Group::make()
                ->schema([
                    Schemas\Components\Section::make('Gambar Produk')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->image()
                                ->directory('products') // Masuk folder public/storage/products
                                ->maxSize(2048) // Max 2MB
                                ->imageEditor(),
                        ]),
                ])->columnSpan(1),
        ];
    }
}
