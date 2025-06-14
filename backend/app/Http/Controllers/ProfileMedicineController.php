<?php

namespace App\Http\Controllers;

use App\Models\ProfileMedicine;
use Illuminate\Http\Request;
use App\Models\HealthProfile;

class ProfileMedicineController extends Controller
{
    public function index(HealthProfile $healthProfile)
    {
        return $healthProfile->profileMedicines()->with('medicine')->get();
    }

    public function store(Request $request, HealthProfile $healthProfile)
    {
        $medicines = $request->validate([
            'medicines' => 'required|array',
            'medicines.*.medicine_id' => 'required|exists:medicines,id',
            'medicines.*.dosage_per_time' => 'required|integer',
            'medicines.*.time_of_day' => 'required|string',
            'medicines.*.times_per_day' => 'required|integer',
            'medicines.*.total_quantity' => 'required|integer',
            'medicines.*.start_date' => 'required|date',
            'medicines.*.notes' => 'nullable|string',
        ]);

        $medicinesWithProfile = collect($medicines['medicines'])->map(function ($medicine) use ($healthProfile) {
            $medicine['health_profile_id'] = $healthProfile->id;
            return $medicine;
        });

        $healthProfile->profileMedicines()->createMany($medicinesWithProfile->toArray());

        return response()->json([
            'message' => 'Medicines added successfully',
            'medicines' => $medicinesWithProfile,
        ], 201);
    }
}
