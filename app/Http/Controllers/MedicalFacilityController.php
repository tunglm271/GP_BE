<?php

namespace App\Http\Controllers;

use App\Models\Role\MedicalFacility;
use App\Http\Requests\StoreMedicalFacilityRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateMedicalFacilityRequest;
use Gate;
use App\Models\Bill;

class MedicalFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = MedicalFacility::select('facility_name','address','phone')->get();
        
        return response()->json(MedicalFacility::all());
    }

    public function show(MedicalFacility $medicalFacility)
    {

        return response()->json($medicalFacility->load(['services','doctors']));
    }


    public function detail(Request $request) {
        $medicalFacility = $request->user()->medicalFacility;
        return response()->json($medicalFacility);
    }

    public function update(UpdateMedicalFacilityRequest $request, MedicalFacility $medicalFacility)
    {
        Gate::authorize('modify', $medicalFacility);
        $fields = $request->validated();
        if($request->hasFile('thumbnail')) {
            $fields['thumbnail'] = cloudinary()->upload($request->file('thumbnail')->getRealPath())->getSecurePath();
        }
        $medicalFacility->update($fields);
        return response()->json( $medicalFacility);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalFacility $medicalFacility)
    {
        Gate::authorize('modify', $medicalFacility);
        $medicalFacility->delete();
        return response()->json(['message' => 'Medical Facility deleted successfully']);
    }

    public function dashboard()
    {
        $medicalFacility = auth()->user()->medicalFacility;
        $totalPatients = $medicalFacility->patients()->count();
        $totalDoctors = $medicalFacility->doctors()->count();
        $totalServices = $medicalFacility->services()->count();
        $appointments = $medicalFacility->appointments()->select('status')->get();

        // Get revenue data for the last 30 days
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $bills = Bill::where('medical_facility_id', $medicalFacility->id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $totalRevenue = $bills->sum('total_amount');
        $dailyRevenue = $bills->where('status', 'paid')
            ->groupBy(function($bill) {
                return \Carbon\Carbon::parse($bill->payment_date)->format('Y-m-d');
            })
            ->map(function($group, $date) {
                return [
                    'date' => $date,
                    'revenue' => $group->sum('total_amount')
                ];
            })
            ->values();

        return response()->json([
            'total_patients' => $totalPatients,
            'total_doctors' => $totalDoctors,
            'total_services' => $totalServices,
            'appointments' => $appointments,
            'total_revenue' => $totalRevenue,
            'daily_revenue' => $dailyRevenue
        ]);
    }
}
