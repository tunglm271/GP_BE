<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role\MedicalFacility;
use App\Models\Role\Doctor;

class MedicalService extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'thumbnail',
        'price',
        'duration',
        'status',
        'service_audience_gender',
        'medical_facility_id',
        'is_public',
        'min_age_requirement',
        'max_age_requirement',
        'instruction_note',
    ];

    public function medicalFacility()
    {
        return $this->belongsTo(MedicalFacility::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctors_medical_services', 'medical_service_id', 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_services', 'medical_service_id', 'bill_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function totalPaidAmount()
    {
        return $this->bills()->sum('total_amount');
    }

    public function scopeSearch($query, $term)
    {
        $term = '%' . $term . '%';
        return $query->where('name', 'like', $term)
                     ->orWhere('description', 'like', $term)
                     ->orWhere('category', 'like', $term);
    }

    public function scopeGender($query, $gender = null)
    {
        if ($gender !== null) {
            return $query->where('service_audience_gender', $gender);
        }
    
        return $query;
    }

    public function scopePrice($query, $min_price = null, $max_price = null)
    {
        if ($min_price !== null) {
            $query->where('price', '>=', $min_price);
        }
        if ($max_price !== null) {
            $query->where('price', '<=', $max_price);
        }
    
        return $query;
    }
}
