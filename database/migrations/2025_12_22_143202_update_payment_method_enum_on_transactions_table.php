<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE transactions 
            MODIFY payment_method 
            ENUM('cash','transfer','qris','midtrans') 
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE transactions 
            MODIFY payment_method 
            ENUM('cash','transfer','qris') 
            NOT NULL
        ");
    }
};
