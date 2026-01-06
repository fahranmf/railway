<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar perintah Artisan kustom Anda.
     */
    protected $commands = [
        // Daftarkan Command yang baru kita buat di atas
        \App\Console\Commands\ZeroStockExpired::class,
    ];

    /**
     * Tentukan kapan perintah dijalankan.
     */
    protected function schedule(Schedule $schedule)
    {
        // Jalankan perintah stock:cleanup setiap hari jam 00:01 (Tengah Malam)
        $schedule->command('stock:cleanup')->dailyAt('00:01');
    }

    /**
     * Register perintah console.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
