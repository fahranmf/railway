<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke User (Pembeli)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Info Order
            $table->string('order_number')->unique(); // Contoh: INV-20240101-001
            $table->decimal('total_amount', 12, 0); // Total Belanja

            // Status
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, failed

            // Snapshot Data (Penting: disimpan disini agar jika user ubah profil, data order lama tidak berubah)
            $table->text('shipping_address');
            $table->string('customer_phone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
