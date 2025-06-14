<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorType extends Model
{
    public function indicatorGroup()
    {
        return $this->belongsTo(IndicatorGroup::class, 'indicator_group_id');
    }
}
