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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('pending');
            $table->string('reason')->nullable();
            $table->string('reject_reason')->nullable();
            $table->date('result_release_date')->nullable();
            $table->boolean('is_follow_up')->default(false);
            $table->timestamps();

            $table->foreignId('health_profile_id')->constrained('health_profiles')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->foreignId('medical_service_id')->constrained('medical_services')->nullable();
            $table->foreignId('facility_id')->constrained('medical_facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
