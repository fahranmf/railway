<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->nullable()->after('unit_cost');
            }
        });

        // Make expired_date nullable so supplier fallback can be applied
        // NOTE: This requires doctrine/dbal to be installed for change().
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->date('expired_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'selling_price')) {
                $table->dropColumn('selling_price');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->date('expired_date')->nullable(false)->change();
        });
    }
};
