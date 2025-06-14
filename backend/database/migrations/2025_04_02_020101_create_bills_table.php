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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->float('total_amount');
            $table->enum('status',['pending','paid','failed'])->default('pending');
            $table->foreignId('medical_facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('health_profile_id')->constrained()->onDelete('cascade');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->default('vnpay');
            $table->string('transaction_id')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('card_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
