<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->decimal('employee_commission', 8, 2)->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->dropColumn('employee_commission');
        });
    }
};