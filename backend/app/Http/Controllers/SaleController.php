<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $facility = $request->user()->medicalFacility;
        return $facility->sales()->with("medicalService")->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'medical_service_id' => 'required|exists:medical_services,id',
            'value' => 'required|numeric',
            'type' => 'required|in:amount,percent',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $medicalService = MedicalService::findOrFail($request->medical_service_id);
        if ($medicalService->medicalFacility->id !== $request->user()->medicalFacility->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $facility = $request->user()->medicalFacility;
        $sale = $facility->sales()->create($request->all());
        return response()->json($sale, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        Gate::authorize('modify', $sale);
        $data = $request->validate([
            'medical_service_id' => 'required|exists:medical_services,id',
            'value' => 'required|numeric',
            'type' => 'required|in:amount,percent',
            'discount' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $sale->update($data);
        return response()->json($sale, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        Gate::authorize('modify', $sale);
        $sale->delete();
        return response()->json([
            'message' => 'Sale deleted successfully'
        ], 204);
    }
}
