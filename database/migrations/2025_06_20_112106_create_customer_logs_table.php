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
        Schema::create('customer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_gid')->unique()->nullable();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->date('next_meeting')->nullable();
            
            // Product and Sales Fields
            $table->string('product_purchased')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->string('masseuse_name')->nullable();
            $table->decimal('massage_price', 10, 2)->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            
            // Other Fields
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_logs');
    }
};