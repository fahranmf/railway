<?php

namespace App\Filament\Resources\Purchases;

use App\Filament\Resources\Purchases\Pages;
use App\Models\Purchase;
use App\Models\ProductBatch;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use UnitEnum;
use BackedEnum;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-truck';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pembelian';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->label('Supplier')
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('purchase_date')
                    ->label('Tanggal Beli')
                    ->default(now())
                    ->required(),

                TextInput::make('reference_no')
                    ->label('No Faktur Supplier')
                    ->placeholder('Contoh: INV-001'),

                Select::make('status')
                    ->options([
                        'ordered' => 'Dipesan (Belum Datang)',
                        'received' => 'Diterima (Masuk Stok)',
                    ])
                    ->default('received')
                    ->required(),

                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),

                Repeater::make('items')
                    ->relationship()
                    ->label('Daftar Barang Belanjaan')
                    ->schema([
                        Grid::make(6)
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produk')
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $product = Product::find($state);

                                            if ($product) {
                                                // harga jual dari product
                                                if (isset($product->price)) {
                                                    $set('selling_price', $product->price);
                                                }

                                                // simpan kategori & unit sebagai hidden state (opsional)
                                                if (isset($product->category_id)) {
                                                    $set('category_id', $product->category_id);
                                                    $set('category_name', optional($product->category)->name);
                                                }
                                                if (isset($product->unit_id)) {
                                                    $set('unit_id', $product->unit_id);
                                                    $set('unit_name', optional($product->unit)->name);
                                                }
                                            }

                                            // Ambil batch terakhir untuk harga beli / expired default
                                            $lastBatch = ProductBatch::where('product_id', $state)
                                                ->where('purchase_price', '>', 0)
                                                ->latest('created_at')
                                                ->first();

                                            if ($lastBatch) {
                                                if ($lastBatch->purchase_price) {
                                                    $set('unit_cost', $lastBatch->purchase_price);
                                                }

                                                if ($lastBatch->expired_date) {
                                                    $set('expired_date', $lastBatch->expired_date->toDateString());
                                                }
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->label('Nama Produk'),
                                        TextInput::make('sku')->required()->label('SKU')->default(fn () => 'SKU-' . strtoupper(str()->random(6))),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->label('Kategori')
                                            ->preload()
                                            ->default(fn () => Category::first()?->id),
                                        Select::make('unit_id')
                                            ->relationship('unit', 'name')
                                            ->label('Satuan')
                                            ->preload()
                                            ->default(fn () => Unit::first()?->id),
                                        TextInput::make('price')->label('Harga Jual')->numeric()->required()->prefix('Rp'),
                                    ]),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($get, $set) => self::calculateTotal($get, $set)),

                                TextInput::make('unit_cost')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->readonly()
                                    ->default(0)
                                    ->placeholder('Otomatis dari Supplier')
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($get, $set) => self::calculateTotal($get, $set)),

                                TextInput::make('selling_price')
                                    ->label('Harga Jual')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->columnSpan(3)
                                    ->reactive(),

                                TextInput::make('category_name')
                                    ->label('Category')
                                    ->disabled()
                                    ->columnSpan(3)
                                    ->placeholder('-'),

                                TextInput::make('unit_name')
                                    ->label('Unit')
                                    ->disabled()
                                    ->columnSpan(3)
                                    ->placeholder('-'),

                                DatePicker::make('expired_date')
                                    ->label('Exp Date')
                                    ->required(false)
                                    ->readonly()
                                    ->columnSpan(6)
                                    ->placeholder('Otomatis dari Supplier'),
                            ]),
                    ])
                    ->createItemButtonLabel('Tambah Barang')
                    ->reactive()
                    ->afterStateUpdated(fn ($get, $set) => self::calculateTotal($get, $set)),

                TextInput::make('total_amount')
                    ->label('Grand Total')
                    ->numeric()
                    ->readOnly()
                    ->prefix('Rp'),
            ]);
    }

    // PERBAIKAN: Hapus tipe data 'Get' dan 'Set' di sini juga
    public static function calculateTotal($get, $set)
    {
        $items = $get('items');
        $grandTotal = 0;

        if ($items) {
            foreach ($items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $price = floatval($item['unit_cost'] ?? 0);
                $grandTotal += ($qty * $price);
            }
        }

        // set with 2 decimal points
        $set('total_amount', round($grandTotal, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_date')->date()->label('Tgl Beli'),
                Tables\Columns\TextColumn::make('reference_no')->label('No Faktur')->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR')->label('Total'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ordered' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
