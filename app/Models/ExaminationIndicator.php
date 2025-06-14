<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationIndicator extends Model
{
    protected $table = 'examination_indicator';
    protected $fillable = [
        'examination_id',
        'indicator_type_id',
        'value',
        'evaluation'
    ];

    protected $appends = ['group_name'];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function indicatorType()
    {
        return $this->belongsTo(IndicatorType::class);
    }

    public function getGroupNameAttribute()
    {
        return $this->indicatorType->indicatorGroup->name ?? null;
    }
}
