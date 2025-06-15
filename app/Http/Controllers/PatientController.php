<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use App\Models\Role\MedicalFacility;
use App\Models\Role\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $facility = $request->user()->medicalFacility;
        if ($facility) {
            return $facility->patients->load(['allergies', 'diseases']);
        }
        return response()->json(['message' => 'Facility not found'], 404);
    }


    public function indexByDoctor(Request $request)
    {
        $doctor = $request->user()->doctor;
        if ($doctor) {
            return $doctor->patients;
        }
        return response()->json(['message' => 'Doctor not found'], 404);
    }

    public function show(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }

    public function homePage(Request $request)
    {
        $facilities = MedicalFacility::inRandomOrder()->limit(5)->get();
        $services = MedicalService::inRandomOrder()->limit(6)->get();
        $appointments = $request->user()->patient->HealthProfiles->map(function ($healthProfile) {
            return $healthProfile->appointments->load(['medicalService','medicalFacility']);
        })->flatten()->unique('id');

        return response()->json([
            'facilities' => $facilities,
            'services' => $services,
            'appointments' => $appointments,
        ]);
    }

    public function diagnosis(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string|max:255',
        ]);

        $service = MedicalService::distinct()->pluck('name')->toArray();

        $symptoms = $request->input('symptoms');
        $response = Http::post('http://localhost:5000/answer', [
        'symptoms' => $symptoms,
        'services' => $service,
    ]);
        // Here you would typically call a service to process the symptoms
        // and return a diagnosis. For now, we will just return the symptoms.
        if ($response->successful()) {
            return response()->json([
                'answer' => $response->json()['answer']
            ]);
        } else {
            return response()->json(['error' => 'Model API call failed'], 500);
        }
    }

    public function landingPage(Request $request)
    {
        $facilities = MedicalFacility::inRandomOrder()->limit(6)->get();
        $services = MedicalService::inRandomOrder()->limit(6)->get();

        return response()->json([
            'facilities' => $facilities,
            'services' => $services,
        ]);
    }
}
