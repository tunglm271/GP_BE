<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role\Patient;

class AdminPatientController extends Controller
{
    public function index()
    {
        return Patient::all();
    }

    public function show(Patient $patient)
    {
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Patient retrieved successfully',
            'patientAccount' => $patient->load(['healthProfiles','healthProfiles.diseases', 'healthProfiles.allergies']),
            'heatlhProfiles' => $patient->healthProfiles->load(['diseases', 'allergies', 'appointments','appointments.doctor', 'appointments.medicalService', 'appointments.medicalFacility']), 
        ]);
    }

    public function activate(Patient $patient)
    {
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $patient->user->update(['active' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Patient activated successfully',
        ]);
    }
    public function deactivate(Patient $patient)
    {
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $patient->user->update(['active' => false]);
        return response()->json([
            'status' => true,
            'message' => 'Patient deactivated successfully',
        ]);
    }
}
