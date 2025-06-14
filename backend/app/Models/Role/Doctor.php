<?php

namespace App\Models\Role;

use App\Models\HealthProfile;
use App\Models\MedicalService;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Role\MedicalFacility;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\Role\DoctorFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'medical_facility_id',
        'name',
        'phone',
        'position',
        'specialization',
        'about',
    ];

    protected $appends = ['email', 'status'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalFacility()
    {
        return $this->belongsTo(MedicalFacility::class);
    }

    public function handleService() {
        return $this->belongsToMany(MedicalService::class, 'doctors_medical_services', 'doctor_id', 'medical_service_id');
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function patients()
    {
        return $this->hasManyThrough(HealthProfile::class, Appointment::class,'doctor_id', 'id', 'id', 'health_profile_id')->distinct();
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getStatusAttribute()
    {
        return $this->user->active ? 'active' : 'inactive';
    }
}
