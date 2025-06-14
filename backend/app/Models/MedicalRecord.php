<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role\Doctor;
use Illuminate\Support\Str;
use App\Models\Appointment;

class MedicalRecord extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'health_profile_id',
        'doctor_id',
        'appointment_id',
        'diagnosis',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // hoặc tạo chuỗi custom
            }
        });
    }

    public function healthProfile()
    {
        return $this->belongsTo(HealthProfile::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function followUpAppointment()
    {
        return $this->belongsTo(Appointment::class, 'follow_up_appointment_id');
    }
}
