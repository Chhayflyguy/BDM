<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('next_booking_date')->nullable()->after('vip_card_expires_at');
            $table->timestamp('booking_completed_at')->nullable()->after('next_booking_date');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['next_booking_date', 'booking_completed_at']);
        });
    }
};