<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_profile_id',
        'medicine_id',
        'dosage_per_time',
        'time_of_day',
        'times_per_day',
        'total_quantity',
        'notes',
        'start_date',
    ];

    public function logs()
    {
        return $this->hasMany(ProfileMedicineLog::class);
    }

    public function healthProfile()
    {
        return $this->belongsTo(HealthProfile::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
