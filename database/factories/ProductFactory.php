<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Daftar Nama Obat Realistis
        $drugNames = [
            'Paracetamol 500mg', 'Amoxicillin 500mg', 'Panadol Extra',
            'Bodrex Migra', 'Promag Tablet', 'Sangobion Kapsul',
            'Imboost Force', 'Betadine Antiseptik', 'Minyak Kayu Putih',
            'Vitamin C IPI', 'Enervon C', 'Insto Tetes Mata',
            'Antangin Cair', 'Tolak Angin', 'Mylanta Cair',
            'Komix OBH', 'Siladex Cough', 'Vicks Formula 44'
        ];

        // Ambil Kategori & Unit secara acak dari database yang sudah ada
        $category = Category::inRandomOrder()->first();
        $unit = Unit::inRandomOrder()->first();

        return [
            'category_id' => $category?->id ?? 1, // Fallback ke ID 1 jika kosong
            'unit_id' => $unit?->id ?? 1,
            'name' => fake()->randomElement($drugNames) . ' (' . fake()->numerify('##') . ')', // Tambah angka biar unik
            'sku' => fake()->unique()->ean13(), // Generate Barcode
            'price' => fake()->numberBetween(5, 150) * 1000, // Harga kelipatan 1000 (Rp 5.000 - 150.000)
            'image' => null, // Gambar kosong dulu
            'is_active' => true,
        ];
    }
}
