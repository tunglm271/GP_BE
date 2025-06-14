<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role\MedicalFacility;

class City extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function medicalFacilities()
    {
        return $this->hasMany(MedicalFacility::class);
    }
}
