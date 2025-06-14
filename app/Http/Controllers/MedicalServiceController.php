<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use App\Http\Requests\StoreMedicalServiceRequest;
use App\Http\Requests\UpdateMedicalServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\Role\MedicalFacility;
use Illuminate\Support\Facades\Cache;

class MedicalServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:facility,admin')->only(['store', 'update', 'destroy']);
        $this->middleware('role:facility, admin, doctor')->only(['indexByFacility']);
    }

    public function index(Request $request)
    {
        $search = $request->query('q');
        $gender = $request->query('gender');
        $min_price = $request->query('minPrice');
        $max_price = $request->query('maxPrice');
        return response()->json([
            "services" => MedicalService::search($search)->gender($gender)->price($min_price, $max_price)->get(),
            "facilities" => MedicalFacility::select('id','facility_name','address','phone','logo')->get(),
        ]);
    }

    public function indexByFacility(Request $request)
    {
        return $request->user()->medicalFacility->services()->with('doctors')->get();
    }

    public function indexByDoctor(Request $request)
    {
        $doctor = $request->user()->doctor;
        return $doctor->handleService;
    }

    public function store(StoreMedicalServiceRequest $request)
    {   
        $fields = $request->validated();
        if($request->hasFile('thumbnail')) {
            $fields['thumbnail'] = cloudinary()->upload($request->file('thumbnail')->getRealPath())->getSecurePath();
        }
        $fields['medical_facility_id'] = $request->user()->medicalFacility->id;
        $medicalService = MedicalService::create($fields);
        $doctor_ids = json_decode($request->doctors);
        $medicalService->doctors()->sync($doctor_ids);
        return response()->json($medicalService, 201);
    }

    public function show(MedicalService $medicalService, Request $request)
    {
        return $medicalService->load(['medicalFacility','doctors','appointments']);
    }

    public function showByPatient(MedicalService $medicalService)
    {
        $service = $medicalService->load(['medicalFacility','doctors','appointments']);
        $recommmend_services = MedicalService::where('id', '!=', $medicalService->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();
        return response()->json([
            'service' => $service,
            'recommmend_services' => $recommmend_services
        ]);
    }

    public function update(UpdateMedicalServiceRequest $request, MedicalService $medicalService)
    {   
        Gate::authorize('modify', $medicalService);
        $fields = $request->validated();
        if($request->hasFile('thumbnail')) {
            $fields['thumbnail'] = cloudinary()->upload($request->file('thumbnail')->getRealPath())->getSecurePath();
        }
        $medicalService->update($fields);
        $doctor_ids = json_decode($request->doctors);
        $medicalService->doctors()->sync($doctor_ids);
        return response()->json($medicalService->load(['doctors']), 200);
    }

    public function destroy(MedicalService $medicalService)
    {   
        Gate::authorize('modify', $medicalService);
        $medicalService->delete();
        return response()->json("delete sucessfully", 204);
    }


    public function getAvaliableSlots(Request $request)
    {
        $id = $request->query('id');
        $date = $request->query('date');
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayMap = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
        ];
        $dayName = $dayMap[$dayOfWeek];

        $medicalService = MedicalService::find($id);
        $doctors = $medicalService->doctors()->with('schedule')->get();
        $slots = [];
        foreach ($doctors as $doctor) {
            foreach ($doctor->schedule as $schedule) {
                if ($schedule->day_of_week != $dayName) {
                    continue;
                }
                $startTime = Carbon::createFromTimeString($schedule->start_time);
                $endTime = Carbon::createFromTimeString($schedule->end_time);
    
                // Làm tròn thời gian bắt đầu lên giờ chẵn
                if ($startTime->minute > 0) {
                    $startTime->addHour()->minute(0)->second(0);
                }
    
                // Làm tròn thời gian kết thúc xuống giờ chẵn
                if ($endTime->minute > 0) {
                    $endTime->minute(0)->second(0);
                }
    
                // Tạo các ca khám 1 giờ
                while ($startTime < $endTime) {
                    $slotStart = $startTime->format('H:i:s');
                    $slotEnd = $startTime->copy()->addHour()->format('H:i:s');
    
                    // Kiểm tra số bệnh nhân bác sĩ đã đảm nhận trong ca khám này
                    $patientCount = Appointment::where('date', $date)
                        ->where('start_time', $slotStart)
                        ->where('end_time', $slotEnd)
                        ->where('doctor_id', $doctor->id)
                        ->count();
    
                    // Xác định key cho ca khám
                    $slotKey = $slotStart . '-' . $slotEnd;
    
                    // Nếu ca khám đã tồn tại, cộng dồn số lượng bác sĩ
                    if (isset($slots[$slotKey])) {
                        $slots[$slotKey]['available'] += (3 - $patientCount);
                    } else {
                        // Tạo mới ca khám
                        $slots[$slotKey] = [
                            'start_time' => $slotStart,
                            'end_time' => $slotEnd,
                            'available' => 3 - $patientCount,
                        ];
                    }
    
                    // Tăng thời gian thêm 1 giờ để tạo ca tiếp theo
                    $startTime->addHour();
                }
            }
        }
    
        // Chuyển đổi mảng từ dạng key-value sang mảng thuần túy
        $result = array_values($slots);
    
        return response()->json($result);
    }

    public function getAvaliableSlotsForDoctor(Request $request)
    {

        $date = $request->query('date');
        $doctor = $request->user()->doctor;
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayMap = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
        ];
        $dayName = $dayMap[$dayOfWeek];
        $schedule = $doctor->schedule()->where('day_of_week', $dayName)->first();
        if(!$schedule) {
            return response()->json([]);
        }
        $startTime = Carbon::createFromTimeString($schedule->start_time);
        $endTime = Carbon::createFromTimeString($schedule->end_time);
        // Làm tròn thời gian bắt đầu lên giờ chẵn
        if ($startTime->minute > 0) {
            $startTime->addHour()->minute(0)->second(0);
        }
        // Làm tròn thời gian kết thúc xuống giờ chẵn
        if ($endTime->minute > 0) {
            $endTime->minute(0)->second(0);
        }
        // Tạo các ca khám 1 giờ
        $slots = [];
        while ($startTime < $endTime) {
            $slotStart = $startTime->format('H:i:s');
            $slotEnd = $startTime->copy()->addHour()->format('H:i:s');

            // Kiểm tra số bệnh nhân bác sĩ đã đảm nhận trong ca khám này
            $patientCount = Appointment::where('date', $date)
                ->where('start_time', $slotStart)
                ->where('end_time', $slotEnd)
                ->where('doctor_id', $doctor->id)
                ->count();

            // Xác định key cho ca khám
            $slotKey = $slotStart . '-' . $slotEnd;

            // Nếu ca khám đã tồn tại, cộng dồn số lượng bác sĩ
            if (isset($slots[$slotKey])) {
                $slots[$slotKey]['available'] += (3 - $patientCount);
            } else {
                // Tạo mới ca khám
                $slots[$slotKey] = [
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'available' => 3 - $patientCount,
                ];
            }

            // Tăng thời gian thêm 1 giờ để tạo ca tiếp theo
            $startTime->addHour();
        }
        // Chuyển đổi mảng từ dạng key-value sang mảng thuần túy
        $result = array_values($slots);
        return response()->json($result);
    }

    public function getDoctorsForAppointment(Request $request)
    {
        $id = $request->query('id');
        $appointmentId = $request->query('appointment_id');
        $appointment = Appointment::find($appointmentId);
        $medicalService = MedicalService::find($id);
        $doctors = $medicalService->doctors()->with('schedule')->get();
        
        // Lấy tổng số cuộc hẹn của mỗi bác sĩ trong ngày
        $doctorAppointments = Appointment::whereIn('doctor_id', $doctors->pluck('id'))
            ->where('date', $appointment->date)
            ->selectRaw('doctor_id, COUNT(*) as total_appointments')
            ->groupBy('doctor_id')
            ->pluck('total_appointments', 'doctor_id')
            ->toArray();

        $valiabeDoctors = [];
        $minAppointments = PHP_INT_MAX;
        
        foreach($doctors as $doctor) {
            $appointmentCount = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $appointment->date)
                ->where('start_time', $appointment->start_time)
                ->where('end_time', $appointment->end_time)
                ->count();
                
            if($appointmentCount < 3) {
                $totalAppointments = $doctorAppointments[$doctor->id] ?? 0;
                $minAppointments = min($minAppointments, $totalAppointments);
                
                $doctorData = $doctor->toArray();
                $doctorData['recommend'] = false; // Mặc định là false
                $doctorData['total_appointments'] = $totalAppointments;
                
                $valiabeDoctors[] = $doctorData;
            }
        }
        
        // Đánh dấu recommend cho những bác sĩ có số lượng cuộc hẹn ít nhất
        foreach($valiabeDoctors as &$doctor) {
            if($doctor['total_appointments'] === $minAppointments) {
                $doctor['recommend'] = true;
            }
        }
        
        return response()->json($valiabeDoctors);
    }

    public function autoAssignAllPendingAppointments(Request $request)
    {
        // Kiểm tra quyền của người dùng (chỉ cơ sở y tế hoặc admin mới có quyền này)
        // Giả định người dùng đăng nhập là MedicalFacility hoặc Admin
        $medicalFacility = $request->user()->medicalFacility; // Lấy cơ sở y tế từ người dùng hiện tại

        if (!$medicalFacility) {
            return response()->json(['error' => 'Bạn không có quyền truy cập tính năng này hoặc không phải là cơ sở y tế.'], 403);
        }

        // Lấy tất cả các đơn khám đang chờ xử lý của cơ sở y tế này
        // Giả định có trường 'status' trong bảng appointments, ví dụ 'pending', 'assigned', 'completed'
        // Và có trường 'doctor_id' là null khi chưa được chỉ định
        $pendingAppointments = Appointment::where('medical_facility_id', $medicalFacility->id)
            ->whereNull('doctor_id') // Chưa có bác sĩ chỉ định
            ->where('status', 'pending') // Đang ở trạng thái chờ
            ->with('medicalService') // Eager load dịch vụ để lấy bác sĩ liên quan
            ->orderBy('date') // Sắp xếp để xử lý các cuộc hẹn sớm hơn trước
            ->orderBy('start_time')
            ->get();

        if ($pendingAppointments->isEmpty()) {
            return response()->json(['message' => 'Không có đơn khám nào đang chờ để tự động chỉ định.'], 200);
        }

        $assignedResults = [];
        $unassignedResults = [];

        // --- Chuẩn bị dữ liệu tải trọng hiện tại của bác sĩ (tối ưu hóa truy vấn DB) ---
        // Lấy tất cả các bác sĩ thuộc cơ sở y tế này và lịch làm việc của họ
        $allFacilityDoctors = $medicalFacility->doctors()->with('schedule', 'handleService')->get()->keyBy('id');

        // Lấy tất cả các ngày duy nhất từ các đơn khám pending để tối ưu truy vấn lịch sử
        $uniqueDates = $pendingAppointments->pluck('date')->unique();

        // Lấy tổng số cuộc hẹn của mỗi bác sĩ cho các ngày liên quan (tính đến thời điểm hiện tại)
        $initialDoctorDailyLoads = Appointment::whereIn('doctor_id', $allFacilityDoctors->keys())
            ->whereIn('date', $uniqueDates)
            ->selectRaw('doctor_id, date, COUNT(*) as total_appointments')
            ->groupBy('doctor_id', 'date')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->doctor_id}-{$item->date}" => $item->total_appointments];
            })
            ->toArray();
        
        // Lấy số cuộc hẹn của mỗi bác sĩ theo từng slot (tính đến thời điểm hiện tại)
        $initialDoctorSlotLoads = Appointment::whereIn('doctor_id', $allFacilityDoctors->keys())
            ->whereIn('date', $uniqueDates)
            ->selectRaw('doctor_id, date, start_time, end_time, COUNT(*) as slot_appointments')
            ->groupBy('doctor_id', 'date', 'start_time', 'end_time')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->doctor_id}-{$item->date}-{$item->start_time}-{$item->end_time}" => $item->slot_appointments];
            })
            ->toArray();
        
        // Tạo bản sao để cập nhật trong bộ nhớ trong quá trình duyệt
        $currentDoctorDailyLoads = $initialDoctorDailyLoads;
        $currentDoctorSlotLoads = $initialDoctorSlotLoads;

        // --- Bắt đầu quá trình tự động chỉ định ---
        foreach ($pendingAppointments as $appointment) {
            $serviceId = $appointment->medical_service_id;
            $appointmentDate = $appointment->date;
            $appointmentStartTime = $appointment->start_time;
            $appointmentEndTime = $appointment->end_time;

            $dayOfWeek = Carbon::parse($appointmentDate)->dayOfWeek;
            $dayMap = [
                0 => 'Chủ Nhật', 1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư',
                4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy',
            ];
            $dayName = $dayMap[$dayOfWeek];

            $medicalService = $appointment->medicalService; // Đã eager loaded

            if (!$medicalService) {
                $unassignedResults[] = ['appointment_id' => $appointment->id, 'reason' => 'Dịch vụ y tế không tồn tại.'];
                continue;
            }

            // Lấy các bác sĩ có thể xử lý dịch vụ này
            // Filter doctors who handle this service
            $doctorsForService = $medicalService->doctors->filter(function($doctor) use ($allFacilityDoctors) {
                return $allFacilityDoctors->has($doctor->id); // Ensure doctor belongs to this facility
            });

            $candidateDoctors = [];

            foreach ($doctorsForService as $doctor) {
                $doctorSchedule = $doctor->schedule->where('day_of_week', $dayName)->first();

                if (!$doctorSchedule) {
                    continue; // Bác sĩ không có lịch làm việc vào ngày này
                }

                $scheduleStart = Carbon::createFromTimeString($doctorSchedule->start_time);
                $scheduleEnd = Carbon::createFromTimeString($doctorSchedule->end_time);
                $slotStart = Carbon::createFromTimeString($appointmentStartTime);
                $slotEnd = Carbon::createFromTimeString($appointmentEndTime);

                // Đảm bảo slot yêu cầu nằm trong lịch làm việc của bác sĩ
                if (!($slotStart->greaterThanOrEqualTo($scheduleStart) && $slotEnd->lessThanOrEqualTo($scheduleEnd))) {
                    continue;
                }

                // Lấy số bệnh nhân hiện tại của bác sĩ trong slot này (từ dữ liệu trong bộ nhớ)
                $slotLoadKey = "{$doctor->id}-{$appointmentDate}-{$appointmentStartTime}-{$appointmentEndTime}";
                $patientCountInSlot = $currentDoctorSlotLoads[$slotLoadKey] ?? 0;

                if ($patientCountInSlot < 3) { // Vẫn còn chỗ trống trong ca khám
                    $candidateDoctors[] = $doctor;
                }
            }

            if (empty($candidateDoctors)) {
                $unassignedResults[] = ['appointment_id' => $appointment->id, 'reason' => 'Không có bác sĩ rảnh cho khung giờ này.'];
                continue;
            }

            // Tìm bác sĩ có tải trọng nhẹ nhất trong ngày từ các ứng viên
            $bestDoctor = null;
            $minDailyAppointments = PHP_INT_MAX;

            foreach ($candidateDoctors as $doctor) {
                $dailyLoadKey = "{$doctor->id}-{$appointmentDate}";
                $currentDoctorDailyLoad = $currentDoctorDailyLoads[$dailyLoadKey] ?? 0;

                if ($currentDoctorDailyLoad < $minDailyAppointments) {
                    $minDailyAppointments = $currentDoctorDailyLoad;
                    $bestDoctor = $doctor;
                }
            }

            if ($bestDoctor) {
                // Đề xuất chỉ định bác sĩ này
                $assignedResults[] = [
                    'appointment_id' => $appointment->id,
                    'assigned_doctor_id' => $bestDoctor->id,
                    'assigned_doctor_name' => $bestDoctor->name, // Giả định có trường 'name'
                    'appointment_date' => $appointmentDate,
                    'appointment_time' => "{$appointmentStartTime} - {$appointmentEndTime}",
                    'daily_load_before_assign' => $minDailyAppointments,
                ];

                // Cập nhật tải trọng trong bộ nhớ để ảnh hưởng đến các lần chỉ định tiếp theo trong cùng batch
                $dailyLoadKey = "{$bestDoctor->id}-{$appointmentDate}";
                $slotLoadKey = "{$bestDoctor->id}-{$appointmentDate}-{$appointmentStartTime}-{$appointmentEndTime}";
                
                $currentDoctorDailyLoads[$dailyLoadKey] = ($currentDoctorDailyLoads[$dailyLoadKey] ?? 0) + 1;
                $currentDoctorSlotLoads[$slotLoadKey] = ($currentDoctorSlotLoads[$slotLoadKey] ?? 0) + 1;

                // In a real application, you would update the database here,
                // likely within a transaction after the loop, e.g.:
                // $appointment->doctor_id = $bestDoctor->id;
                // $appointment->status = 'assigned';
                // $appointment->save(); 
            } else {
                $unassignedResults[] = ['appointment_id' => $appointment->id, 'reason' => 'Không tìm được bác sĩ tối ưu để chỉ định.'];
            }
        }

        // Trả về kết quả đề xuất. Quá trình lưu vào DB sẽ diễn ra ở một bước sau đó
        // (ví dụ: trong một transaction, hoặc sau khi người dùng xác nhận)
        return response()->json([
            'message' => 'Kết quả tự động chỉ định đơn khám:',
            'assigned_appointments' => $assignedResults,
            'unassigned_appointments' => $unassignedResults,
            'total_pending_processed' => $pendingAppointments->count(),
            'total_assigned' => count($assignedResults),
            'total_unassigned' => count($unassignedResults),
        ]);
    }
}
