<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private $defaultThumbnail = 'https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/logo_processed_diojsx';


    public function up(): void
    {
        Schema::create('medical_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("category");
            $table->string('thumbnail')->default($this->defaultThumbnail);
            $table->decimal('price',10,2)->default(0);
            $table->integer('duration')->nullable();
            $table->enum('status',['active', 'inactive'])->default('active');
            $table->string('instruction_note')->nullable();
            $table->enum('service_audience_gender',['male','female','both'])->default('both');
            $table->integer('min_age_requirement')->nullable();
            $table->integer('max_age_requirement')->nullable();
            $table->boolean('is_public')->default(true);
            $table->foreignId('medical_facility_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_services');
    }
};
