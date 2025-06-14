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
        Schema::create('profile_medicine_logs', function (Blueprint $table) {
             $table->id();
            $table->foreignId('profile_medicine_id')->constrained()->onDelete('cascade');
            $table->date('intake_date'); // ngày uống thuốc
            $table->string('time_slot'); // ví dụ: '08:00'
            $table->boolean('taken')->default(false); // đã uống hay chưa
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_medicine_logs');
    }
};
