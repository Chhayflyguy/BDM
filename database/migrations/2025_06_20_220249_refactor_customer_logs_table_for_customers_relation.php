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
            // Add the new foreign key after the user_id column
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->onDelete('set null');

            // Drop old columns that are now stored in the customers table
            if (Schema::hasColumn('customer_logs', 'customer_gid')) {
                $table->dropColumn('customer_gid');
            }
            if (Schema::hasColumn('customer_logs', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('customer_logs', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->string('customer_gid')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
        });
    }
};