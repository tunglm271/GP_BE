<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Bill;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $appointments = [
                [
                    'date' => now()->addDays(1)->toDateString(),
                    'start_time' => '09:00',
                    'end_time' => '10:00',
                    'status' => 'assigned',
                    'reason' => 'General check-up',
                    'health_profile_id' => 1,
                    'doctor_id' => 1,
                    'medical_service_id' => 1,
                    'facility_id' => 1,
                ],
                [
                    'date' => now()->addDays(2)->toDateString(),
                    'start_time' => '11:00',
                    'end_time' => '12:00',
                    'status' => 'assigned',
                    'reason' => 'Follow-up consultation',
                    'health_profile_id' => 2,
                    'doctor_id' => 2,
                    'medical_service_id' => 2,
                    'facility_id' => 2,
                ],
            ];

            foreach ($appointments as $appointmentData) {
                $appointment = Appointment::create($appointmentData);

                Bill::create([
                    'health_profile_id' => $appointment->health_profile_id,
                    'medical_facility_id' => $appointment->facility_id,
                    'appointment_id' => $appointment->id,
                    'status' => 'pending',
                    'payment_method' => 'vnpay',
                    'total_amount' => 100000,
                ]);
            }
        });
    }
}
