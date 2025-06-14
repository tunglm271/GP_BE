<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role\Patient;
use App\Models\HealthProfile;
use App\Models\Role\MedicalFacility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Patient $patient;
    protected HealthProfile $healthProfile;
    protected MedicalFacility $facility;
    protected $medicalService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed dữ liệu cơ bản (cities, types,...)
        $this->seed();

        // Lấy user bệnh nhân thực tế đã có trong DB
        $this->user = User::where('role_id', 2)->firstOrFail();
        $this->patient = $this->user->patient;

        // Lấy health profile đầu tiên của patient
        $this->healthProfile = $this->patient->healthProfiles()->firstOrFail();

        // Lấy 1 cơ sở y tế và dịch vụ
        $this->facility = MedicalFacility::firstOrFail();
        $this->medicalService = $this->facility->services()->firstOrFail();
    }

    public function test_patient_can_create_appointment()
    {
        $payload = [
            'medical_service_id' => $this->medicalService->id,
            'health_profile_id' => $this->healthProfile->id,
            'date' => '2025-06-10',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'reason' => 'Khám tổng quát',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/appointments', $payload);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Appointment created successfully']);
    }

    // public function test_non_patient_cannot_create_appointment()
    // {
    //     $nonPatient = User::where('role_id', 3)->firstOrFail();

    //     $response = $this->actingAs($nonPatient)->postJson('/api/appointments', [
    //         'medical_service_id' => $this->medicalService->id,
    //         'health_profile_id' => $this->healthProfile->id,
    //         'date' => '2025-06-10',
    //         'start_time' => '09:00:00',
    //         'end_time' => '10:00:00',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_validation_error_when_missing_required_fields()
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/appointments', [
    //         // không gửi dữ liệu gì để kiểm tra lỗi validation
    //     ]);

    //     $response->assertStatus(422)
    //              ->assertJsonValidationErrors([
    //                  'medical_service_id',
    //                  'health_profile_id',
    //                  'date',
    //                  'start_time',
    //                  'end_time'
    //              ]);
    // }

    // public function test_cannot_create_with_invalid_health_profile()
    // {
    //     $otherProfile = HealthProfile::findOrFail($this->healthProfile->id + 1); // giả sử đây là profile không thuộc về user hiện tại

    //     $response = $this->actingAs($this->user)->postJson('/api/appointments', [
    //         'medical_service_id' => $this->medicalService->id,
    //         'health_profile_id' => $otherProfile->id,
    //         'date' => '2025-06-10',
    //         'start_time' => '09:00:00',
    //         'end_time' => '10:00:00',
    //     ]);

    //     $response->assertStatus(404)
    //              ->assertJson(['message' => 'User not allowed to book for this profile.']);
    // }

    // public function test_reason_exceeds_max_length()
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/appointments', [
    //         'medical_service_id' => $this->medicalService->id,
    //         'health_profile_id' => $this->healthProfile->id,
    //         'date' => '2025-06-10',
    //         'start_time' => '09:00:00',
    //         'end_time' => '10:00:00',
    //         'reason' => str_repeat('a', 256), // vượt quá giới hạn 255 ký tự
    //     ]);

    //     $response->assertStatus(422)
    //              ->assertJsonValidationErrors(['reason']);
    // }
}
