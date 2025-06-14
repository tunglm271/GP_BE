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
        Schema::create('doctors_medical_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_service_id')->constrained()->onDelete('cascade');

            $table->unique(['doctor_id', 'medical_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors_medical_services');
    }
};
