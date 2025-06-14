<?php

namespace App\Http\Controllers;

use App\Models\Role\Doctor;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DoctorMail;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $facility_id = $request->user()->medicalFacility->id;
        $doctors = Doctor::where('medical_facility_id', $facility_id)->with('user','handleService')->get();
        return response()->json($doctors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        $fields = $request->validated();
        $random_password = Str::random(8);

        $doctor = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($random_password),
            'role_id' => 3,
        ]);

        $doctor->doctor()->create([
            'medical_facility_id' => $request->user()->medicalFacility->id,
            'specialization' => $fields['specialization'],
            'about' => $fields['about'] ?? null,
            'name' => $fields['name'],
            'phone ' => $fields['phone'] ?? null,
        ]);

        $data = [
            'facility' => $request->user()->medicalFacility->name,
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => $random_password,
        ];

        Mail::to($fields['email'])->queue(new DoctorMail($data));
        if($request->input('schedule')) {
            $schedule = json_decode($request->input('schedule'));
            foreach ($schedule as $day => $times) {
                foreach ($times as $time) {
                    Schedule::create([
                        'doctor_id' => $doctor->doctor->id,
                        'day_of_week' => $day,
                        'start_time' => $time->start,
                        'end_time' => $time->end,
                    ]);
                }
            }
        }

        return response()->json($doctor, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return $doctor->load(['user', 'schedule','appointments','appointments.healthProfile', 'appointments.medicalService']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        //
    }

    public function getDoctorSchedule(Request $request) {
        $schedules = $request->user()->doctor->schedule;
        $appointments = $request->user()->doctor->appointments;
        return response()->json([
            'schedules' =>$schedules,
            'appointments' => $appointments->load(['healthProfile','medicalRecord','medicalService']),
        ]);
    }

    public function updateDoctorSchedule(Request $request) {
        $doctor = $request->user()->doctor;
        $schedule = json_decode($request->input('schedule'), true);

        \DB::transaction(function () use ($doctor, $schedule) {
            // Clear existing schedules
            $doctor->schedule()->delete();

            // Create new schedules
            foreach ($schedule as $day => $times) {
                foreach ($times as $time) {
                    Schedule::create([
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => $time['start'],
                        'end_time' => $time['end'],
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Schedule updated successfully']);
    }
}
