<?php

namespace App\Filament\Widgets;

use App\Models\ProductBatch;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class ExpiringProducts extends BaseWidget
{
    // Tampil di urutan kedua (Bawah Statistik)
    protected static ?int $sort = 2;

    // Lebar widget full (sepanjang layar)
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '⚠️ Peringatan Stok Kadaluarsa (30 Hari)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductBatch::query()
                    ->with('product')
                    // Filter: Hanya yang expirednya dalam 30 hari ke depan
                    ->where('expired_date', '<=', now()->addDays(30))
                    // Filter: Hanya yang stoknya masih ada
                    ->where('stock', '>', 0)
                    // Urutkan dari yang paling cepat basi
                    ->orderBy('expired_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Obat')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('batch_number')
                    ->label('No. Batch')
                    ->copyable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->badge(),

                Tables\Columns\TextColumn::make('expired_date')
                    ->label('Tgl Kadaluarsa')
                    ->date('d F Y')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if (empty($state)) {
                            return 'secondary';
                        }

                        $date = Carbon::parse($state);
                        $today = Carbon::today();

                        if ($date->lt($today)) {
                            return 'danger';
                        }

                        $days = $today->diffInDays($date);

                        if ($days <= 7) {
                            return 'danger';
                        }

                        if ($days <= 30) {
                            return 'warning';
                        }

                        return 'success';
                    }),
            ]);
    }
}
