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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('The user who created this customer profile');
            $table->string('customer_gid')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->integer('age')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->json('health_conditions')->nullable();
            $table->json('problem_areas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};