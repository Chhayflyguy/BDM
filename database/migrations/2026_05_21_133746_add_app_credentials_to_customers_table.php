<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds app credentials (password + registered_by) to customers.
     * Also makes phone required and unique for use as login identifier.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // App password (bcrypt hashed) — required when admin creates customer
            $table->string('app_password')->nullable()->after('phone');

            // Track how the customer was registered
            $table->enum('registered_by', ['admin', 'self'])->default('admin')->after('app_password');

            // Make phone required and unique (used as login identifier)
            // First drop the old nullable phone column definition and re-add as unique
            $table->string('phone', 20)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['app_password', 'registered_by']);
            $table->string('phone', 20)->nullable()->change();
        });
    }
};
