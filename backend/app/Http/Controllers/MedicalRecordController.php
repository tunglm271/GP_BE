<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalRecordController extends Controller
{
    public function show(MedicalRecord $medicalRecord)
    {
        $data = $medicalRecord->load([
            'healthProfile',
            'doctor' => function ($query) {
                $query->select('id', 'name', 'phone', 'specialization', 'user_id');
            },
            'appointment.medicalFacility' => function ($query) {
                $query->select('id', 'address', 'facility_name', 'phone','user_id');
            },
            'examinations.indicators.indicatorType',
            'prescription.medicines',
            'followUpAppointment'
        ]);

        return response()->json($data);
    }

    public function loadPrescription(Prescription $prescription)
    {
        $medicines = $prescription->medicines()->withPivot('usage', 'amount')->get();

        return response()->json($medicines);
    }

    public function getPrescriptionMedicines(Prescription $prescription)
    {
        $medicines = $prescription->medicines()
            ->with(['medicine:id,name,unit,description'])
            ->withPivot('usage', 'amount')
            ->get();

        return response()->json($medicines);
    }

    public function precriptionDownload(MedicalRecord $medicalRecord)
    {
        $patient = $medicalRecord->healthProfile;
        $doctor = $medicalRecord->doctor;
        $facility = $doctor->medicalFacility;
        $medicines = $medicalRecord->prescription->medicines;
        $pdf = Pdf::loadView('pdf.prescription', compact('patient', 'doctor', 'medicines', 'medicalRecord', 'facility'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        $fileName = 'don-thuoc-' . $patient->name . '.pdf';
        return $pdf->download($fileName);
    }
}
