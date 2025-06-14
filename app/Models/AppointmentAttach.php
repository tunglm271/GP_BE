<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAttach extends Model
{
    protected $table = 'appointment_attachs';
    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'file_extension',
        'appointment_id',
    ];
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
