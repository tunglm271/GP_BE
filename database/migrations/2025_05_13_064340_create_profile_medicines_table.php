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
        Schema::create('profile_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->integer('dosage_per_time'); // số viên/lần
            $table->string('time_of_day')->nullable(); // thời gian uống thuốc
            $table->integer('times_per_day')->default(1); // số lần uống/ngày
            $table->date('start_date'); // ngày bắt đầu uống thuốc
            $table->integer('total_quantity')->default(0); // tổng số viên còn lại
            $table->string('notes')->nullable(); // ghi chú như "sau khi ăn"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_medicines');
    }
};
