<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationImage extends Model
{
    protected $table = 'examination_image';
    protected $fillable = [
        'examination_id',
        'image_path',
    ];
}
