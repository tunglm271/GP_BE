<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'prescription_medicine')
            ->withPivot('usage', 'amount');
    }
}
