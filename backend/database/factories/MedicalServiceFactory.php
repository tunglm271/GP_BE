<?php

namespace Database\Factories;

use App\Models\Role\MedicalFacility;
use App\Models\MedicalService;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalServiceFactory extends Factory
{
    protected $model = MedicalService::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['general', 'dental', 'ophthalmology', 'dermatology']),
            'thumbnail' => null, // hoặc $this->faker->imageUrl(640, 480, 'medical'),
            'price' => $this->faker->numberBetween(100000, 5000000), // giá theo VND ví dụ
            'duration' => $this->faker->numberBetween(15, 120), // phút
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'service_audience_gender' => $this->faker->randomElement([null, 'male', 'female']),
            'medical_facility_id' => MedicalFacility::factory(),
            'is_public' => $this->faker->boolean(80), // 80% chance true
            'min_age_requirement' => $this->faker->optional()->numberBetween(0, 50),
            'max_age_requirement' => $this->faker->optional()->numberBetween(50, 100),
            'instruction_note' => $this->faker->optional()->text(100),
        ];
    }
}