<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductBatch; // Import Model Batch
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin (Owner)
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // 2. Buat User Staff (Kasir)
        User::firstOrCreate(
            ['email' => 'kasir@admin.com'],
            [
                'name' => 'Kasir',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );

        // 3. Jalankan Seeder Master Data (Kategori, Unit, Supplier)
        $this->call(MasterDataSeeder::class);

        // 4. GENERATE PRODUK + BATCH OTOMATIS (LOGIKA FEFO)
        // Artinya: Buat 25 Produk.
        // SETIAP 1 Produk, buatkan 3 Batch Stok yang expired-nya beda-beda.
        Product::factory(25)
            ->has(ProductBatch::factory()->count(3), 'batches')
            ->create();
    }
}
