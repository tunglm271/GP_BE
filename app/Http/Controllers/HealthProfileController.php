<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use App\Http\Requests\StoreHealthProfileRequest;
use App\Http\Requests\UpdateHealthProfileRequest;
use Illuminate\Http\Request;

class HealthProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $profiles = HealthProfile::where('patient_id', $request->user()->patient->id)->with(['diseases','allergies'])->get();
        return $profiles;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHealthProfileRequest $request)
    {
        $validatedData = $request->validated();

        $healthProfile = new HealthProfile($validatedData);
        $healthProfile->patient_id = $request->user()->patient->id;
    
        if ($request->hasFile('avatar')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('avatar')->getRealPath())->getSecurePath();
            $healthProfile->avatar = $uploadedFileUrl;
        }
        
        $healthProfile->save();

        if($request->has('allergies')) {
            $allergyIds = json_decode($request->input('allergies'));
            $healthProfile->allergies()->sync($allergyIds);
        }

        if ($request->has('diseases')) {
            $diseaseIds = json_decode($request->input('diseases'));
            $healthProfile->diseases()->sync($diseaseIds);
        }
    
        return response()->json([
            'message' => 'Health profile created successfully',
            'health_profile_id' => $healthProfile->id
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthProfile $healthProfile)
    {
        return $healthProfile->load([
            'diseases',
            'allergies',
            'appointments' => function($query) {
                $query->whereNotNull('result_release_date')->with(['medicalFacility','medicalRecord']);
            },
            'appointments.medicalService',
            'appointments.doctor',
            'appointments.medicalFacility',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthProfileRequest $request, HealthProfile $healthProfile)
    {
        $validatedData = $request->validated();
        $healthProfile->update($validatedData);

        if ($request->hasFile('avatar')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('avatar')->getRealPath())->getSecurePath();
            $healthProfile->avatar = $uploadedFileUrl;
            $healthProfile->save();
        }

        $healthProfile->save();
        
        if($request->has('allergies')) {
            $allergyIds = json_decode($request->input('allergies'));
            $healthProfile->allergies()->sync($allergyIds);
        }

        if ($request->has('diseases')) {
            $diseaseIds = json_decode($request->input('diseases'));
            $healthProfile->diseases()->sync($diseaseIds);
        }
        
        return response()->json([
            'message' => 'Health profile updated successfully',
            'health_profile' => $healthProfile
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthProfile $healthProfile)
    {
        $healthProfile->delete();

        return response()->json([
            'message' => 'Health profile deleted successfully'
        ], 200);
    }

    public function getMedicalRecords(HealthProfile $healthProfile)
    {
        return $healthProfile->medicalRecords()->with(['doctor','examinations'])->get();
    }
}
