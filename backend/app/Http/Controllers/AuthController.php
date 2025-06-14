<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Google_Client;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        try {
            // Bắt đầu transaction
            DB::beginTransaction();
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 2,
                'last_activity' => now(),
            ]);

            $patient = $user->patient()->create([
                'name' => $request->name,
            ]);
            $patient->healthProfiles()->create([
                'name' => $request->name,
                'relationship' => 'Self',
            ]);
    
            // Tạo token sau khi hoàn tất
            $token = $user->createToken($user->email)->plainTextToken;
    
            // Xác nhận transaction
            DB::commit();
    
            return response()->json([
                'message' => 'User registered successfully', 
                'user' => $user,
                'token' => $token
            ], 201);
    
        } catch (\Exception $e) {
            // Nếu có lỗi, rollback transaction
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'details' => $e->getMessage()], 500);
        }
    }

    public function facilityRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'facility_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'legal_representative_name' => 'required|string|max:255',
            'legal_representative_id' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 4,
            ]);

            $user->medicalFacility()->create([
                'facility_name' => $request->facility_name,
                'phone' => $request->phone ?? null,
                'address' => $request->address,
                'description' => $request->description ?? null,
                'status' => 'pending',
                'working_time' => $request->working_time ?? null,
                'thumbnail' => $request->thumbnail ?? null,
                'logo' => $request->logo ?? null,
                'website' => $request->website ?? null,
                'lat' => $request->lat ?? null,
                'lng' => $request->lng ?? null,
                'legal_representative_name' => $request->legal_representative_name,
                'legal_representative_id' => $request->legal_representative_id,
                'tax_code' => $request->tax_code ?? null,
                'medical_practice_license' => $request->medical_practice_license,
                'issuance_date' => $request->issuance_date,
                'issuance_place' => $request->issuance_place,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User registered successfully', 
                'user' => $user,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'details' => $th->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
    
        // Tìm user theo email
        $user = User::where('email', $request->email)->first();
    
        // Kiểm tra mật khẩu
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Xóa token cũ nếu cần
        $user->tokens()->delete();
    
        // Tạo token mới
        $token = $user->createToken('api-token')->plainTextToken;

        // Cập nhật last_activity
        $user->last_activity = now();
        $user->save();
        
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        if($request->hasFile('avatar')) {
            $avatar_url = cloudinary()->upload($request->file('avatar')->getRealPath())->getSecurePath();
            $user->avatar = $avatar_url;
        }

        if(isset($data['new_password']) && Hash::check($data['current_password'], $user->password)) {
            $user->password = Hash::make($data['new_password']);
        }

        if(isset($data['name'])) {
            $user->name = $data['name'];
        }
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function loginWithGoogle(Request $request)
    {
        $idToken = $request->input('credential');

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Validate token against your app's Client ID

        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'];


            $user = User::where('email', $email)->first();

            if (!$user) {
                // Nếu chưa có thì tạo user mới
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'role_id' => 2,
                    'last_activity' => now(),
                ]);
            }

            $patient = $user->patient()->create([
                'name' => $name,
            ]);
            $patient->healthProfiles()->create([
                'name' =>$name,
                'relationship' => 'Self',
            ]);

            // Đăng nhập user
            $token = $user->createToken('google-login')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }
    }
}
