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
        Schema::create('pay_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Transaction details
            $table->string('transaction_id')->unique(); // e.g., PAY-2025-001
            $table->enum('transaction_type', ['salary', 'bonus', 'deduction', 'advance'])->default('salary');
            $table->decimal('amount', 10, 2);
            $table->date('pay_date');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            
            // Period covered
            $table->integer('period_month'); // 1-12
            $table->integer('period_year');  // e.g., 2025
            
            // Payment method
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'other'])->default('cash');
            
            // Additional info
            $table->text('notes')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users'); // Who processed payment
            $table->timestamp('paid_at')->nullable(); // When marked as paid
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['employee_id', 'period_year', 'period_month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_transactions');
    }
};
