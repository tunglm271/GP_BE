<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfileMedicine;
use App\Models\ProfileMedicineLog;
use Carbon\Carbon;
use App\Models\HealthProfile;

class ProfileMedicineLogController extends Controller
{
    public function getByDate(Request $request, HealthProfile $healthProfile)
    {
        $date = Carbon::parse($request->query('date'))->startOfDay();
        $today = now()->startOfDay();

        $medicines = ProfileMedicine::with(['medicine', 'logs'])
            ->where('health_profile_id', $healthProfile->id)
            ->get();

        $result = [];

        foreach ($medicines as $med) {
            $startDate = Carbon::parse($med->start_date)->startOfDay();
            $totalLogsTaken = $med->logs->where('taken', true)->count();
            $remainingDoses = $med->total_quantity - $totalLogsTaken;

            if ($remainingDoses <= 0) {
                continue; // Thuốc đã uống hết
            }

            // Bỏ qua nếu ngày được chọn nhỏ hơn ngày bắt đầu
            if ($date->lt($startDate)) {
                continue;
            }

            // Nếu là ngày hôm nay hoặc trong quá khứ
            if ($date->lte($today)) {
                // Lấy các log trong ngày
                $logsForDate = $med->logs->where('date', $date->toDateString())
                    ->map(function ($log) {
                        return [
                            'time_of_day' => $log->time_of_day,
                            'taken' => $log->taken,
                        ];
                    })
                    ->values();

                $result[] = [
                    'profile_medicine_id' => $med->id,
                    'medicine_name' => $med->medicine->name,
                    'dosage_per_time' => $med->dosage_per_time,
                    'times_per_day' => $med->times_per_day,
                    'remaining_doses' => $remainingDoses,
                    'date' => $date->toDateString(),
                    'unit' => $med->medicine->unit,
                    'logs' => $logsForDate,
                    'status' => 'past_or_today',
                ];
            } else {
                // Nếu là ngày tương lai, hiển thị thuốc cần uống
                $daysPassed = $today->diffInDays($startDate, false);
                $daysSinceStart = $date->diffInDays($startDate, false);

                $maxDoseDay = floor($remainingDoses / $med->times_per_day);
                if ($daysSinceStart >= $maxDoseDay) {
                    continue; // Thuốc không đủ dùng đến ngày tương lai này
                }

                // Trả về kế hoạch uống thuốc cho ngày tương lai
                $futureLogs = [];
                for ($i = 0; $i < $med->times_per_day; $i++) {
                    $futureLogs[] = [
                        'time_of_day' => 'time_' . ($i + 1), // nếu bạn có time_of_day chi tiết thì sửa lại
                        'taken' => false,
                    ];
                }

                $result[] = [
                    'profile_medicine_id' => $med->id,
                    'medicine_name' => $med->medicine->name,
                    'dosage_per_time' => $med->dosage_per_time,
                    'times_per_day' => $med->times_per_day,
                    'remaining_doses' => $remainingDoses,
                    'unit' => $med->medicine->unit,
                    'date' => $date->toDateString(),
                    'logs' => $futureLogs,
                    'status' => 'future',
                ];
            }
        }

        return response()->json($result);
    }

    public function check(Request $request)
    {
        $request->validate([
            'profile_medicine_id' => 'required|exists:profile_medicines,id',
            'date' => 'required|date',
            'time_of_day' => 'required|string', // ví dụ: 'morning', 'noon', 'evening'
        ]);

        $profileMedicineId = $request->input('profile_medicine_id');
        $date = Carbon::parse($request->input('date'))->toDateString();
        $timeOfDay = $request->input('time_of_day');

        // Tìm hoặc tạo mới log
        $log = ProfileMedicineLog::firstOrCreate(
            [
                'profile_medicine_id' => $profileMedicineId,
                'date' => $date,
                'time_of_day' => $timeOfDay,
            ],
            [
                'is_taken' => true,
            ]
        );

        // Nếu đã có log nhưng chưa uống thì cập nhật lại
        if (!$log->wasRecentlyCreated && !$log->is_taken) {
            $log->update(['is_taken' => true]);
        }

        return response()->json([
            'message' => 'Thuốc đã được đánh dấu là đã uống.',
            'log' => $log,
        ]);
    }

}
