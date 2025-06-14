<?php

namespace Database\Factories;

use App\Models\Role\Patient;
use App\Models\HealthProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthProfileFactory extends Factory
{
    protected $model = HealthProfile::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'relationship' => $this->faker->randomElement(['self', 'spouse', 'child', 'parent', 'other']),
            'avatar' => null, // hoặc bạn có thể tạo avatar url giả: $this->faker->imageUrl(200, 200, 'people'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'height' => $this->faker->numberBetween(140, 200), // cm
            'weight' => $this->faker->numberBetween(40, 120),  // kg
            'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'),
            'patient_id' => Patient::factory(), // tự động tạo Patient nếu không truyền explicit
            'medical_insurance_number' => strtoupper($this->faker->bothify('??######')), // ví dụ mã bảo hiểm
        ];
    }
}
