<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Danh sách role_id tương ứng
        $roleMapping = [
            'admin'    => 1,
            'patient'  => 2,
            'doctor'   => 3,
            'facility' => 4,
        ];
        
        // Lấy role_id của user hiện tại
        $userRole = $request->user()->role_id;

        // Chuyển các role thành role_id để so sánh
        $allowedRoleIds = array_map(fn($role) => $roleMapping[$role] ?? null, $roles);

        // Kiểm tra xem role của user có thuộc danh sách role cho phép không
        if (!in_array($userRole, $allowedRoleIds)) {
            return response()->json(['message' => 'Method is not allowed'], 401);
        }

        return $next($request);
    }
}
