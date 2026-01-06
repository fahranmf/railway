<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();

            // Relasi ke Tabel Product (Milik Alyshia)
            // cascadeOnDelete: Jika produk dihapus, stok batch ikut hilang
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('batch_number')->unique(); // Kode Batch (cth: BATCH-001)
            $table->integer('stock')->default(0);     // Jumlah Stok per batch
            $table->date('expired_date');             // Tanggal Kadaluarsa
            $table->decimal('purchase_price', 15, 2)->nullable(); // Harga Beli (Modal)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
