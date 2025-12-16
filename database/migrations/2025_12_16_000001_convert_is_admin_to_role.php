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
        Schema::table('users', function (Blueprint $table) {
            // Add new role column
            $table->enum('role', ['admin', 'staff'])->default('staff')->after('id');
            // Add created_by to track which admin created the user
            $table->foreignId('created_by')->nullable()->after('remember_token')->constrained('users')->onDelete('set null');
        });

        // Migrate existing data: convert is_admin boolean to role
        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);
        DB::table('users')->where('is_admin', false)->update(['role' => 'staff']);

        // Drop the old is_admin column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-add is_admin column
            $table->boolean('is_admin')->default(false)->after('id');
        });

        // Migrate data back: convert role to is_admin
        DB::table('users')->where('role', 'admin')->update(['is_admin' => true]);
        DB::table('users')->where('role', 'staff')->update(['is_admin' => false]);

        // Drop the new columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['role', 'created_by']);
        });
    }
};
