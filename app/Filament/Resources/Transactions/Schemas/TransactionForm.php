<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\ProductBatch;
use App\Models\Product;
use Filament\Schemas;
use Filament\Forms;
use Filament\Forms\Components\Repeater;

class TransactionForm
{
    public static function schema(): array
    {
        return [
            // --- KOLOM KIRI (Info & Keranjang) - Span 2 ---
            Schemas\Components\Group::make()
                ->schema([
                    // SECTION 1: INFO TRANSAKSI
                    Schemas\Components\Section::make('Info Transaksi')
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Customer')
                                ->default(auth()->id())
                                ->required()
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Tunai',
                                    'transfer' => 'Transfer Bank',
                                    'qris' => 'QRIS',
                                ])
                                ->required()
                                ->default('cash'),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'pending' => 'Pending (Belum Bayar)',
                                    'paid' => 'Lunas',
                                    'cancel' => 'Dibatalkan',
                                ])
                                ->required()
                                ->default('pending'),
                        ])->columns(3),

                    // SECTION 2: KERANJANG BELANJA
                    Schemas\Components\Section::make('Keranjang Belanja')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    // [PENTING] Field untuk ID Batch (Required + Hidden)
                                    Forms\Components\Hidden::make('product_batch_id')
                                        ->required()
                                        ->dehydrated(),

                                    // 1. Pilih Obat
                                    Forms\Components\Select::make('product_id')
                                        ->label('Pilih Obat')
                                        ->relationship('product', 'name')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            // A. Ambil Harga
                                            $product = Product::find($state);
                                            $set('price', $product?->price ?? 0);

                                            // B. FEFO Logic (Otomatis pilih batch)
                                            if ($state) {
                                                $batch = ProductBatch::where('product_id', $state)
                                                    ->where('stock', '>', 0)
                                                    ->where('expired_date', '>=', now())
                                                    ->orderBy('expired_date', 'asc')
                                                    ->first();

                                                if ($batch) {
                                                    $set('product_batch_id', $batch->id);
                                                } else {
                                                    $set('product_batch_id', null);
                                                }
                                            }
                                        })
                                        ->columnSpan(4),

                                    // 2. Quantity
                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->live()
                                        ->columnSpan(2),

                                    // 3. Harga
                                    Forms\Components\TextInput::make('price')
                                        ->label('@Harga')
                                        ->numeric()
                                        ->readOnly()
                                        ->prefix('Rp')
                                        ->columnSpan(3),

                                    // 4. Subtotal Display
                                    Forms\Components\Placeholder::make('subtotal_display')
                                        ->label('Subtotal')
                                        ->content(fn ($get) => 'Rp ' . number_format((int)$get('quantity') * (int)$get('price'), 0, ',', '.'))
                                        ->columnSpan(3),

                                    // Info Batch (Visual)
                                    Forms\Components\Placeholder::make('batch_info')
                                        ->label('')
                                        ->content(function ($get) {
                                            $id = $get('batches.batch_number');
                                            if(!$id) return '⚠️ Stok/Batch Kosong';
                                            $b = ProductBatch::find($id);
                                            return $b ? "batches: {$b->batch_number} (Exp: {$b->expired_date->format('d/m/Y')})" : '-';
                                        })
                                        ->extraAttributes(['class' => 'text-xs text-gray-500'])
                                        ->columnSpan(12),
                                ])
                                ->columns(12)
                                ->live()
                                ->afterStateUpdated(function ($get, $set) {
                                    $items = $get('items');
                                    $sum = collect($items)->reduce(fn ($carry, $item) => $carry + ($item['quantity'] * $item['price']), 0);
                                    $set('total_amount', $sum);
                                }),
                        ]),
                ])
                ->columnSpan(2), // Tutup Group Kiri

            // --- KOLOM KANAN (Total Bayar) - Span 1 ---
            Schemas\Components\Group::make()
                ->schema([
                    Schemas\Components\Section::make('Total Tagihan')
                        ->schema([
                            Forms\Components\TextInput::make('total_amount')
                                ->label('GRAND TOTAL')
                                ->numeric()
                                ->readOnly()
                                ->prefix('Rp')
                                ->default(0)
                                ->extraInputAttributes(['style' => 'font-size: 1.5rem; font-weight: bold; color: green;']),
                        ]),
                ])
                ->columnSpan(1), // Tutup Group Kanan
        ];
    }
}
