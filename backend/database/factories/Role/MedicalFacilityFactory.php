<?php

namespace Database\Factories\Role;

use App\Models\User;
use App\Models\Role\FacilityType;
use App\Models\City;
use App\Models\Role\MedicalFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalFacilityFactory extends Factory
{
    protected $model = MedicalFacility::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // tạo user mới nếu không truyền
            'facility_name' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->text(200),
            'status' => $this->faker->randomElement(['done', 'rejected']),
            'working_time' => '08:00 - 17:00',
            'thumbnail' => null, // hoặc $this->faker->imageUrl(640, 480, 'medical'),
            'logo' => null,
            'website' => $this->faker->url(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'legal_representative_name' => $this->faker->name(),
            'legal_representative_id' => $this->faker->numerify('###########'), // giả số CMND/CCCD
            'tax_code' => $this->faker->bothify('TAX-#####'),
            'medical_practice_license' => $this->faker->bothify('LIC-#####'),
            'issuance_date' => $this->faker->date('Y-m-d', 'now'),
            'issuance_place' => $this->faker->city(),
            'facility_type_id' => 1,
            'city_id' => 1,
        ];
    }
}
