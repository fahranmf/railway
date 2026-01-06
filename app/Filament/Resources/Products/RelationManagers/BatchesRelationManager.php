<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema; // Tetap pakai Schema
use BackedEnum;

// --- IMPORT KOMPONEN FORM (Hanya Input saja, Section dihapus) ---
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

// --- IMPORT KOMPONEN TABLE ---
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class BatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'batches';

    protected static ?string $title = 'Stok & Kadaluarsa';

    protected static BackedEnum|string|null $icon = 'heroicon-o-clipboard-document-list';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // --- KITA LANGSUNG MASUKKAN INPUT DI SINI (TANPA SECTION) ---

                // Pastikan nama kolom 'batch_number' sesuai database
                TextInput::make('batch_number')
                    ->label('Nomor Batch')
                    ->required()
                    ->default(fn () => 'BATCH-' . strtoupper(uniqid()))
                    ->maxLength(255)
                    ->columnSpanFull(), // Agar lebar penuh

                TextInput::make('stock')
                    ->label('Jumlah Stok Masuk')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                DatePicker::make('expired_date')
                    ->label('Tanggal Kadaluarsa')
                    ->required()
                    ->displayFormat('d F Y'),
                    //->minDate(now()->format('Y-m-d'))

                TextInput::make('purchase_price')
                    ->label('Harga Beli (Modal)')
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('batch_number')
            ->columns([
                TextColumn::make('batch_number')
                    ->label('No. Batch')
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->badge()
                    ->color(fn (string $state): string => $state <= 5 ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('expired_date')
                    ->label('Tgl Kadaluarsa')
                    ->date('d F Y')
                    ->sortable()
                    ->badge()
                    ->color(function (string $state): string {
                        if (empty($state)) {
                            return 'secondary';
                        }

                        $date = \Carbon\Carbon::parse($state);
                        $today = \Carbon\Carbon::today();

                        // Sudah lewat
                        if ($date->lt($today)) {
                            return 'danger';
                        }

                        $days = $today->diffInDays($date);

                        // Akan kedaluwarsa dalam 7 hari -> merah
                        if ($days <= 7) {
                            return 'danger';
                        }

                        // Akan kedaluwarsa dalam 30 hari -> kuning
                        if ($days <= 30) {
                            return 'warning';
                        }

                        return 'success';
                    }),

                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Stok'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
