<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make user_id nullable in all tables so data is preserved when users are deleted
     */
    public function up(): void
    {
        // Make user_id nullable in employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change()->constrained()->onDelete('set null');
        });

        // Make user_id nullable in customer_logs table
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change()->constrained()->onDelete('set null');
        });

        // Make user_id nullable in daily_expenses table
        Schema::table('daily_expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change()->constrained()->onDelete('set null');
        });
        
        // Customers table should already be nullable from previous migration
        // But let's ensure it's set to onDelete('set null')
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable()->change()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore to NOT NULL with CASCADE delete
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });

        Schema::table('customer_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });

        Schema::table('daily_expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });
    }
};
