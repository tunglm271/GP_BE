<?php

namespace App\Http\Controllers;

use App\Models\IndicatorType;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function getIndicatorTypes()
    {
        $indicatorTypes = IndicatorType::with('indicatorGroup')
            ->select('id', 'name', 'unit','indicator_group_id')
            ->get()
            ->map(function ($indicatorType) {
                return [
                    'id' => $indicatorType->id,
                    'name' => $indicatorType->name,
                    'unit' => $indicatorType->unit,
                    'group_name' => $indicatorType->indicatorGroup ? $indicatorType->indicatorGroup->name : null,
                ];
            });

        // Return the indicator types as a JSON response
        return response()->json($indicatorTypes);
    }
}
