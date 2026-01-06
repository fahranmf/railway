<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kita cek dulu biar aman, kalau belum ada baru dibuat
            if (!Schema::hasColumn('transactions', 'total_price')) {
                // Menambahkan kolom total_price (Angka desimal, max 15 digit, 2 desimal)
                $table->decimal('total_price', 15, 2)->default(0)->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'total_price')) {
                $table->dropColumn('total_price');
            }
        });
    }
};
