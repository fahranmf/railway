<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->cascadeOnDelete();

            // [PERBAIKAN 1] Tambahkan product_id agar relasi di Model jalan
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            // [PERBAIKAN 2] Buat batch nullable. Jika batch habis/dihapus, transaksi tetap aman.
            $table->foreignId('product_batch_id')
                  ->nullable() // <--- Tambahkan nullable()
                  ->constrained('product_batches')
                  ->nullOnDelete(); // Jika batch dihapus, set null (jangan hapus transaksi)

            // [PERBAIKAN 3] Pastikan namanya quantity (sesuai database)
            $table->integer('quantity');

            $table->decimal('price', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
