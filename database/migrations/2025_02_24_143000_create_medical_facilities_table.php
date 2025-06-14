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
        Schema::create('medical_facilities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('facility_name');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->text('description')->nullable();
            $table->enum('status', ['unverified', 'pending', 'rejected', 'done'])->default('pending');
            $table->string('working_time')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->float('lat')->nullable()->default(0.00);
            $table->float('lng')->nullable()->default(0.00);

            $table->string('legal_representative_name');
            $table->string('legal_representative_id');
            $table->string('tax_code')->nullable();
            $table->string('medical_practice_license');
            $table->date('issuance_date');
            $table->string('issuance_place');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('facility_type_id')->constrained('facility_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_facilities');
    }
};
