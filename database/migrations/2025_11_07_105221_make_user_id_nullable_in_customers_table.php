<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);
        });
        
        // Make user_id nullable using DB::statement for PostgreSQL compatibility
        DB::statement('ALTER TABLE customers ALTER COLUMN user_id DROP NOT NULL');
        
        Schema::table('customers', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
        });
        
        // Make user_id not nullable again using DB::statement for PostgreSQL compatibility
        DB::statement('ALTER TABLE customers ALTER COLUMN user_id SET NOT NULL');
        
        Schema::table('customers', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
