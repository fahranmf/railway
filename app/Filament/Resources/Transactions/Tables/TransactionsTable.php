<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Actions\Action; // Import Action untuk tombol custom
use Filament\Actions\EditAction;

class TransactionTable
{
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu Transaksi')
                ->dateTime('d M Y H:i')
                ->sortable(),

            Tables\Columns\TextColumn::make('user.name')
                ->label('Pelanggan')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_amount')
                ->label('Total Bayar')
                ->money('IDR')
                ->weight('bold')
                ->sortable(),

            Tables\Columns\TextColumn::make('payment_method')
                ->label('Metode')
                ->badge()
                ->color('info'),

            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                    'cancel' => 'danger',
                }),
        ];
    }

    // --- INI TAMBAHAN BLOCK 11 (TOMBOL PRINT) ---
    public static function actions(): array
    {
        return [
            // Tombol Edit Bawaan
            EditAction::make(),

            // TOMBOL PRINT STRUK
            Action::make('print')
                ->label('Struk')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (Transaction $record) => route('transactions.print', $record))
                ->openUrlInNewTab(), // Buka tab baru agar admin tidak tertutup
        ];
    }
}
