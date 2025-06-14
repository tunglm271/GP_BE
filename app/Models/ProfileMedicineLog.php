<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileMedicineLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_medicine_id',
        'intake_date',
        'time_slot',
        'taken',
        'notes',
    ];

    public function profileMedicine()
    {
        return $this->belongsTo(ProfileMedicine::class);
    }
}