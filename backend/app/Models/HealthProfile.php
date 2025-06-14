<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role\Patient;
use App\Models\Allergy;
use App\Models\Disease;
class HealthProfile extends Model
{
    /** @use HasFactory<\Database\Factories\HealthProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'relationship',
        'avatar',
        'gender',
        'height',
        'weight',
        'date_of_birth',
        'patient_id',
        'medical_insurance_number',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'health_profile_allergy');
    }

    public function diseases() 
    {
        return $this->belongsToMany(Disease::class,"health_profile_disease");
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }


    public function profileMedicines()
    {
        return $this->hasMany(ProfileMedicine::class);
    }

    public function profileMedicineLogs()
    {
        return $this->hasMany(ProfileMedicineLog::class);
    }
}
