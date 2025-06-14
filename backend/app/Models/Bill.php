<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role\MedicalFacility;
class Bill extends Model
{
    protected $fillable = [
        'medical_facility_id',
        'health_profile_id',
        'appointment_id',
        'status',
        'payment_method',
        'transaction_id',
        'total_amount',
        'payment_date',
        'bank_code',
        'card_type',
    ];

    public function healthProfile()
    {
        return $this->belongsTo(HealthProfile::class);
    }

    public function medicalFacility()
    {
        return $this->belongsTo(MedicalFacility::class);
    }

    public function services()
    {
        return $this->belongsToMany(MedicalService::class,'bill_services', 'bill_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
