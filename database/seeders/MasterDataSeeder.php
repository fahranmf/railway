<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED KATEGORI
        $categories = [
            'Obat Bebas', 'Obat Keras', 'Obat Resep',
            'Vitamin & Suplemen', 'Alat Kesehatan', 'Ibu & Anak'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat]
            );
        }

        // 2. SEED SATUAN (UNIT)
        $units = [
            ['name' => 'Tablet', 'symbol' => 'Tab'],
            ['name' => 'Strip', 'symbol' => 'Stp'],
            ['name' => 'Botol', 'symbol' => 'Btl'],
            ['name' => 'Box', 'symbol' => 'Box'],
            ['name' => 'Tube', 'symbol' => 'Tube'],
            ['name' => 'Pieces', 'symbol' => 'Pcs'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                ['name' => $unit['name']]
            );
        }

        // 3. SEED SUPPLIER (5 Data Dummy)
        for ($i = 1; $i <= 5; $i++) {
            Supplier::firstOrCreate(
                ['email' => "supplier{$i}@pharmacy.com"],
                [
                    'name' => 'PT. Distributor Farmasi ' . chr(64 + $i), // Output: PT... A, B, C
                    'contact_person' => 'Sales Manager ' . $i,
                    'phone' => '08123456789' . $i,
                    'address' => "Jl. Gudang Obat No. {$i}, Kawasan Industri Pulo Gadung",
                    'default_expiry_months' => 12,
                ]
            );
        }

        // 4. Sample products (2 items) - created so purchase form's product select has options
        $category = Category::first();
        $unit = Unit::first();

        if ($category && $unit) {
            \App\Models\Product::firstOrCreate(
                ['sku' => 'SKU-AX1234'],
                [
                    'name' => 'Paracetamol 500mg',
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'price' => 2500,
                ]
            );

            \App\Models\Product::firstOrCreate(
                ['sku' => 'SKU-BX5678'],
                [
                    'name' => 'Multivitamin Syrup',
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'price' => 45000,
                ]
            );
        }
    }
}
