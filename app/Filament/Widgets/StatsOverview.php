<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Urutan tampilan (Paling atas)
    protected static ?int $sort = 1;

    // Auto refresh data setiap 15 detik (Realtime)
    public ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // 1. Hitung Total Uang Masuk (Semua transaksi kecuali yang dibatalkan)
        $totalOmzet = Transaction::where('status', '!=', 'cancel')->sum('total_amount');

        // 2. Hitung Jumlah Transaksi (Semua status)
        $totalTransaksi = Transaction::count();

        // 3. Hitung Jumlah Obat Aktif
        $totalProduk = Product::where('is_active', true)->count();

        return [
            Stat::make('Total Omzet', 'Rp ' . number_format($totalOmzet, 0, ',', '.'))
                ->description('Pendapatan Penjualan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success') // Hijau
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]), // Grafik dummy pemanis

            Stat::make('Total Transaksi', $totalTransaksi)
                ->description('Transaksi Penjualan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'), // Warna Utama Theme

            Stat::make('Obat Terdaftar', $totalProduk)
                ->description('Item Obat Aktif')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'), // Kuning
        ];
    }
}
