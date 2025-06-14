<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Faker\Provider\Medical;
use Illuminate\Http\Request;
use App\Models\Role\MedicalFacility;

class AdminFacilityController extends Controller
{
    public function index() {
        return MedicalFacility::all();
    }

    public function show(MedicalFacility $facility) {
        return $facility->load(['services', 'doctors']);
    }

    public function activate(MedicalFacility $facility) {
        $facility->user->active = true;
        $facility->user->save();
        return response()->json(['message' => 'Facility activated successfully']);
    }

    public function deactivate(MedicalFacility $facility) {
        $facility->user->active = false;
        $facility->user->save();
        return response()->json(['message' => 'Facility deactivated successfully']);
    }
}
