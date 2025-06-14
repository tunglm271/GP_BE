<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;  
use App\Models\City;

class CityController extends Controller
{
    public function __invoke()
    {
        $key = 'cities_list';

        // Thử lấy dữ liệu trong Redis
        $cached = Redis::get($key);

        if ($cached) {
            // Redis lưu chuỗi JSON, decode ra mảng / collection
            $cities = json_decode($cached, true);
        } else {
            // Lấy từ DB
            $cities = City::all();

            // Lưu vào Redis dạng JSON, expire 3600 giây (1 tiếng)
            Redis::setex($key, 3600, $cities->toJson());
        }

        return response()->json($cities);
    }
}