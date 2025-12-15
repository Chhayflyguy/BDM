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
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->integer('product_quantity')->nullable()->default(1)->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->dropColumn('product_quantity');
        });
    }
};
