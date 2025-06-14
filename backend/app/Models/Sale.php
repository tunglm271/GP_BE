<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role\MedicalFacility;
use App\Models\MedicalService;

class Sale extends Model
{
    protected $fillable = [
        'medical_service_id',
        'type',
        'value',
        'start_date',
        'end_date',
    ];

    public function medicalService()
    {
        return $this->belongsTo(MedicalService::class);
    }

    public function medicalFacility()
    {
        return $this->hasOneThrough(MedicalFacility::class,MedicalService::class,'id','id','medical_service_id','medical_facility_id');
    }
}
