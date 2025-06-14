<?php

namespace Database\Seeders;
use DB;
use App\Models\User;
use App\Models\Role\Patient;
use App\Models\Role\Doctor;
use App\Models\Role\MedicalFacility;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void

    {
        // User::factory(10)->create();
        $this->call([
            CitySeeder::class,
            MedicineSeeder::class,
            AllergySeeder::class,
            DiseaseSeeder::class,
            FacilityTypeSeeder::class,
            MedicalFacilitySeeder::class,
            UserSeeder::class,
            MedicalServiceSeeder::class,
            IndicatorSeeder::class,
            AppointmentSeeder::class,
            ArticleSeeder::class,
        ]);   
    }
}
