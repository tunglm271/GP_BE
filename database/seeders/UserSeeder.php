<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role\Doctor;
use App\Models\Role\MedicalFacility;
use App\Models\Role\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{    
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
            'role_id' => 1,
            'last_activity' => now(),
        ]);

        DB::transaction(function () {
            // Tạo 2 bệnh nhân
            foreach (['Test Patient 1', 'Test Patient 2'] as $index => $patientName) {
                $patient = User::factory()->create([
                    'name' => $patientName,
                    'email' => 'patient' . ($index + 1) . '@example.com',
                    'password' => Hash::make('12345678'),
                    'role_id' => 2,
                    'last_activity' => now(),
                ]);

                $patientProfile = Patient::create([
                    'user_id' => $patient->id,
                    'name' => $patientName,
                ]);

                // Tạo hồ sơ sức khỏe
                $patientProfile->healthProfiles()->create([
                    'name' => $patientName,
                    'relationship' => 'Self',
                ]);
            }
        });
                
        for($index = 0; $index < 2; $index++) {
            $name = vnfaker()->fullname(3);
            $doctor = User::factory()->create([
            'name' => $name,
            'email' => 'doctor' . ($index + 1) . '@example.com',
            'password' => bcrypt('12345678'),
            'role_id' => 3,
            'last_activity' => now(),
            ]);
            $doctorProfile = Doctor::create([
                'user_id' => $doctor->id,
                'medical_facility_id' => 1,
                'name' => $name,
                'phone' => vnfaker()->mobilephone(10),
                'position' => "Bác sĩ",
                'specialization' => "Chuyên khoa",
                'about' => "Giới thiệu về bác sĩ",
            ]);
            $doctorProfile->schedule()->create([
                'doctor_id' => $doctorProfile->id,
                'day_of_week' => "Thứ Hai",
                'start_time' => '08:00',
                'end_time' => '17:00',
            ]);
            $doctorProfile->schedule()->create([
                'doctor_id' => $doctorProfile->id,
                'day_of_week' => "Thứ Ba",
                'start_time' => '08:00',
                'end_time' => '17:00',
            ]);
            $doctorProfile->schedule()->create([
                'doctor_id' => $doctorProfile->id,
                'day_of_week' => "Thứ Tư",
                'start_time' => '08:00',
                'end_time' => '17:00',
            ]);
            $doctorProfile->schedule()->create([
                'doctor_id' => $doctorProfile->id,
                'day_of_week' => "Thứ Năm",
                'start_time' => '08:00',
                'end_time' => '17:00',
            ]);
        }
    }
}
