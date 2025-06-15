<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicalServiceController;
use App\Http\Controllers\MedicalFacilityController;
use App\Http\Controllers\HealthProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\AllergyController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\admin\AdminFacilityController;
use App\Http\Controllers\admin\AdminPatientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\ProfileMedicineController;
use App\Http\Controllers\ProfileMedicineLogController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\NotificationController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('user/update', [AuthController::class, 'updateUser']);
    Route::get('homepage', [PatientController::class, 'homePage']);
    Route::apiResource('medical-facilities', MedicalFacilityController::class)->except('store');
    Route::get('my-facility', [MedicalFacilityController::class, 'detail']);
    Route::get('medical-facility/dashboard', [MedicalFacilityController::class, 'dashboard']);
    Route::apiResource('doctors', DoctorController::class);
    Route::get("doctor/schedule", [DoctorController::class, 'getDoctorSchedule']);
    Route::post('doctor/schedule', [DoctorController::class, 'updateDoctorSchedule']);
    Route::apiResource('patients', PatientController::class)->except('store');
    Route::get('patient/doctor', [PatientController::class, 'indexByDoctor']);
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('medical-services', MedicalServiceController::class);
    Route::get('patient/medical-services/{medicalService}', [MedicalServiceController::class, 'showByPatient']);
    Route::get('doctor-medical-services', [MedicalServiceController::class, 'indexByDoctor']);
    Route::get('service/valiable-slots', [MedicalServiceController::class, 'getAvaliableSlots']);
    Route::get('service/doctor-slots', [MedicalServiceController::class, 'getAvaliableSlotsForDoctor']);
    Route::get('service/doctors', [MedicalServiceController::class, 'getDoctorsForAppointment']);
    Route::get('my-medical-services', [MedicalServiceController::class, 'indexByFacility']);
    Route::apiResource('health-profiles', HealthProfileController::class);
    Route::get('health-profiles/{healthProfile}/medical-records', [HealthProfileController::class, 'getMedicalRecords']);
    Route::prefix('health-profiles/{healthProfile}')->group(function () {
        Route::get('/medicines', [ProfileMedicineController::class, 'index']);
        Route::post('/medicines', [ProfileMedicineController::class, 'store']);

        Route::get('/medicines/schedules', [ProfileMedicineLogController::class, 'getByDate']);
        Route::get('/medicines/logs', [ProfileMedicineLogController::class, 'dailyLogs']);
    });
    Route::get('diseases', [DiseaseController::class, 'index']);
    Route::get('allergies', [AllergyController::class, 'index']);
    Route::get('medicines', [MedicineController::class, 'index']);
    Route::get('cities', CityController::class);
    Route::get('indicator-types', [IndicatorController::class, 'getIndicatorTypes']);
    Route::apiResource('sales', SaleController::class)->except('show')->middleware('role:facility');
    Route::apiResource('appointments', AppointmentController::class)->except('update');
    Route::get('appoinments-by-profile/{healthProfileId}', [AppointmentController::class, 'indexByHealthProfile']);
    Route::post('appointment/assign-doctor', [AppointmentController::class, 'assignDoctor']);
    Route::get('appointment/facility', [AppointmentController::class, 'indexByFacility']);
    Route::get('appointment/doctor', [AppointmentController::class, 'indexByDoctor']);
    Route::post('appointment/doctor-create', [AppointmentController::class, 'createByDoctor']);
    Route::post('appointments/{appointment}/reject', [AppointmentController::class, 'cancel']);
    Route::get('vnpay_payment', [TransactionController::class, 'vnpay_payment']);
    Route::post('appointments/{appointmentId}/vnpay_verify_payment', [TransactionController::class, 'verifyPayment']);
    Route::get('appointments/{appointment}/bill', [TransactionController::class, 'showBill']);
    Route::get('revenue-statistics', [TransactionController::class, 'getRevenueStats']);
    Route::post('appointments/{appointment}/add-result', [AppointmentController::class, 'addResult']);
    Route::get('medical-records/{medicalRecord}', [MedicalRecordController::class, 'show']);
    Route::get('medical-records/{medicalRecord}/prescription-download', [MedicalRecordController::class, 'precriptionDownload']);
    Route::get('prescriptions/{prescription}', [MedicalRecordController::class, 'loadPrescription']);
    Route::get('prescriptions/{prescription}/medicines', [MedicalRecordController::class, 'getPrescriptionMedicines']);
    Route::get('admin/medical-facilities', [MedicalFacilityController::class, 'index']);
    Route::apiResource('articles', ArticleController::class);
    Route::get('articles/{article}/show-by-patient', [ArticleController::class, 'showByPatient']);
    Route::get('articles-homepage', [ArticleController::class, 'articleHomepage']);
    Route::post('articles/{article}/publish', [ArticleController::class, 'publish']);
    Route::post('articles/{article}/unpublish', [ArticleController::class, 'unpublish']);
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('dashboard', DashboardController::class);
        Route::get('medical-facilities', [AdminFacilityController::class, 'index']);
        Route::get('medical-facilities/{facility}', [AdminFacilityController::class, 'show']);
        Route::post('medical-facilities/{facility}/activate', [AdminFacilityController::class, 'activate']);
        Route::post('medical-facilities/{facility}/deactivate', [AdminFacilityController::class, 'deactivate']);
        Route::get('patient-accounts',[AdminPatientController::class, 'index']);
        Route::get('patient-accounts/{patient}',[AdminPatientController::class, 'show']);
        Route::put('patient-accounts/{patient}/activate',[AdminPatientController::class, 'activate']);
        Route::put('patient-accounts/{patient}/deactivate',[AdminPatientController::class, 'deactivate']);
    })->middleware( 'role:admin');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
Route::post('diagnosis', [DiagnosisController::class, 'query']);
Route::post('rag-diagnosis', [DiagnosisController::class, 'ragQuery']);
Route::get('landing-page', [PatientController::class, 'landingPage']);

