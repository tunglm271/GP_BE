<?php

namespace Database\Factories\Role;

use App\Models\User;
use App\Models\Role\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            // user_id cần truyền vào khi tạo hoặc tự động tạo user mới nếu muốn
            'user_id' => User::factory(), 

            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
        ];
    }
}