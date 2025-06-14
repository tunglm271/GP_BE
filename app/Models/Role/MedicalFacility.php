<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\MedicalService;
use App\Models\Appointment;
use App\Models\FacilityType;
use App\Models\HealthProfile;
use App\Models\City;
use App\Models\Sale;

class MedicalFacility extends Model
{
    /** @use HasFactory<\Database\Factories\Role\MedicalFacilityFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_name',
        'phone',
        'address',
        'description',
        'status',
        'working_time',
        'thumbnail',
        'logo',
        'website',
        'lat',
        'lng',
        'legal_representative_name',
        'legal_representative_id',
        'tax_code',
        'medical_practice_license',
        'issuance_date',
        'issuance_place',
        'facility_type_id',
        'city_id',
    ];

    protected $appends = ['email', 'number_of_services', 'number_of_doctors', 'status', 'facility_type_name', 'city_name'];
    protected $hidden = [
        'facility_type',
        'city',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(MedicalService::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'facility_id');
    }

    public function patients()
    {
        return $this->hasManyThrough(HealthProfile::class, Appointment::class, 'facility_id', 'id', 'id', 'health_profile_id')->distinct();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function sales() 
    {
        return $this->hasManyThrough(Sale::class,MedicalService::class, 'medical_facility_id', 'medical_service_id', 'id', 'id');
    }

    public function facilityType()
    {
        return $this->belongsTo(FacilityType::class);
    }

    public function getEmailAttribute()
    {
        return optional($this->user)->email;
    }

    public function getNumberOfServicesAttribute()
    {
        return $this->services()->count();
    }

    public function getNumberOfDoctorsAttribute()
    {
        return $this->doctors()->count();
    }

    public function getStatusAttribute()
    {
        return optional($this->user)->active ? 'active' : 'inactive';
    }

    public function getCityNameAttribute()
    {
        return optional($this->city)->name;
    }

    public function getFacilityTypeNameAttribute()
    {
        return optional($this->facilityType)->name;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%$search%");
            });
        }
    }

    
}
