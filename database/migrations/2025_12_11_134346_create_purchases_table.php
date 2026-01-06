<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained(); // Beli dari siapa?
            $table->foreignId('user_id')->constrained(); // Siapa admin yang input?
            $table->string('reference_no')->nullable(); // No Faktur dari Supplier
            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['ordered', 'received', 'cancelled'])->default('received');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
