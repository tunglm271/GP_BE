<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $fillable = [
        'medical_record_id',
        'test_name',
        'examination_type',
        'conclusion',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function images()
    {
        return $this->hasMany(ExaminationImage::class);
    }

    public function indicators()
    {
        return $this->hasMany(ExaminationIndicator::class);
    }

}
