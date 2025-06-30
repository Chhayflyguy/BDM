<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('vip_card_id')->nullable()->unique()->after('customer_gid');
            $table->decimal('vip_card_balance', 10, 2)->default(0.00)->after('vip_card_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['vip_card_id', 'vip_card_balance']);
        });
    }
};