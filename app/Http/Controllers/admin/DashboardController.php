<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role\MedicalFacility;
use App\Models\Role\Patient;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Get current month's end date
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Get last month's end date
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Get total facility counts up to each month end and calculate percentage change
        $currentFacilityCount = MedicalFacility::where('created_at', '<=', $currentMonthEnd)->count();
        $lastFacilityCount = MedicalFacility::where('created_at', '<=', $lastMonthEnd)->count();
        $facilityPercentageChange = $lastFacilityCount > 0 
            ? round((($currentFacilityCount - $lastFacilityCount) / $lastFacilityCount) * 100, 1)
            : 0;

        // Get total patient counts up to each month end and calculate percentage change
        $currentPatientCount = Patient::where('created_at', '<=', $currentMonthEnd)->count();
        $lastPatientCount = Patient::where('created_at', '<=', $lastMonthEnd)->count();
        $patientPercentageChange = $lastPatientCount > 0 
            ? round((($currentPatientCount - $lastPatientCount) / $lastPatientCount) * 100, 1)
            : 0;

        // Get total article counts up to each month end and calculate percentage change
        $currentArticleCount = Article::where('created_at', '<=', $currentMonthEnd)->count();
        $lastArticleCount = Article::where('created_at', '<=', $lastMonthEnd)->count();
        $articlePercentageChange = $lastArticleCount > 0 
            ? round((($currentArticleCount - $lastArticleCount) / $lastArticleCount) * 100, 1)
            : 0;

        $data = [
            'facilities' => [
                'count' => MedicalFacility::count(),
                'percentage_change' => $facilityPercentageChange,
                'trend' => $facilityPercentageChange >= 0 ? 'up' : 'down'
            ],
            'patients' => [
                'count' => Patient::count(),
                'percentage_change' => $patientPercentageChange,
                'trend' => $patientPercentageChange >= 0 ? 'up' : 'down'
            ],
            'articles' => [
                'count' => Article::count(),
                'percentage_change' => $articlePercentageChange,
                'trend' => $articlePercentageChange >= 0 ? 'up' : 'down'
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => $data
        ]);
    }
}
