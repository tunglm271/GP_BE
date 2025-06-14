<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role\MedicalFacility;
use Illuminate\Support\Facades\DB;
class MedicalFacilitySeeder extends Seeder
{
     protected $defaultDescription = '
        <h2><strong>Phòng khám Đa khoa - Lựa chọn sức khỏe cho mọi nhà </strong></h2>
        <p>Chăm sóc sức khỏe là yếu tố cần thiết giúp duy trì cuộc sống hạnh phúc cho mọi gia đình.</p>
        <p>Phòng khám Đa khoa là nơi cung cấp dịch vụ chăm sóc sức khỏe toàn diện, từ khám bệnh, điều trị đến tư vấn sức khỏe.</p>
        <p>Với đội ngũ bác sĩ chuyên môn cao, trang thiết bị hiện đại và dịch vụ tận tâm, chúng tôi cam kết mang đến cho bạn và gia đình sự chăm sóc tốt nhất.</p>
        <p>Hãy đến với chúng tôi để trải nghiệm dịch vụ chăm sóc sức khỏe chất lượng cao.</p>
        <p>Chúng tôi luôn sẵn sàng lắng nghe và phục vụ bạn.</p>
        <p>Phòng khám Đa khoa - Lựa chọn sức khỏe cho mọi nhà</p>
        <p>Địa chỉ: 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM</p>
        <p>Điện thoại: 1900 2115</p>
        <p>Website: <a href="https://example.com">https://example.com</a></p>
        <p>Thời gian làm việc: Thứ 2 - Thứ 7: 07:00 - 16:00 | Chủ Nhật: 07:00 - 12:00</p>
    ';


    protected $facilities = [
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility1_dtegiu",
            'facility_name' => 'Phòng khám nhi đồng 2',
            'address' => "222-224-226 Nguyễn Duy Dương, Phường 4, Quận 10, TP.HCM",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility2_y5loth",
            'facility_name' => 'Phòng khám trung ương',
            'address' => "781/B1-B3-B5 Lê Hồng Phong nối dài, Phường 12, Quận 10, TP.HCM",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility3_xnesoj",
            'facility_name' => 'Phòng khám đa khoa minh nhật',
            'address' => "11A Đinh Bộ Lĩnh, Phường 24, Quận Bình Thạnh, Thành phố Hồ Chí Minh",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility4",
            'facility_name' => 'Bệnh Viện Đồng Nai - 2',
            'address' => "B1-B3-B5, Hẻm 781 Lê Hồng Phong, Phường 12, Quận 10, Hồ Chí Minh",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility5",
            'facility_name' => 'Phòng khám Nhi BS Võ Thị My Na',
            'address' => "2/41 Đường ĐT 743B, Khu Phố Bình Đức, Phường Bình Hòa , Thành Phố Thuận An, Tỉnh Bình Dương",
            'phone' => "1900 2115",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility6",
            'facility_name' => 'Phòng Khám Chuyên Khoa Da Liễu Dr. Choice Clinic',
            'address' => "92 Đường D5, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility7",
            'facility_name' => 'Phòng khám Nam khoa & Hiếm muộn Khang Trí - Thạc sĩ Bác sĩ Lê Vũ Tân',
            'address' => "26 Vĩnh Viễn, Phường 12, Quận 10, TPHCM",
            'working_time' => "Thứ 2 - Thứ 6: 16h00 - 19h00 | Thứ 7: 08h30 - 11h00 (Sáng)",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility8",
            'facility_name' => 'Phòng khám Đa khoa Pháp Anh',
            'address' => "222-224-226 Nguyễn Duy Dương, Phường 4, Quận 10, TP.HCM",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility9",
            'facility_name' => 'Bệnh viện đa khoa Singapore (Singapore General Hospital)',
            'address' => "Số 88, Đường số 8, Khu dân cư Trung Sơn, Xã Bình Hưng, Huyện Bình Chánh, TP. Hồ Chí Minh",
        ],
        [
            'logo' => "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/facility10",
            'facility_name' => 'Trung tâm Chẩn đoán Y khoa Kỹ thuật cao Thiện Nhân',
            'address' => "276-278-280 Đống Đa, P.Thanh Bình, Q.Hải Châu, Đà Nẵng",
        ],
    ];



    public function run(): void
    {
          DB::beginTransaction();
        
        try {
            // Prepare data for bulk insertion
            $userData = [];
            $facilityData = [];
            $now = now();
            
            foreach ($this->facilities as $index => $facility) {
                // Create user data for bulk insertion
                $userId = DB::table('users')->insertGetId([
                    'name' => 'Test Facility ' . ($index + 1),
                    'email' => 'facility' . ($index + 1) . '@example.com',
                    'password' => Hash::make('12345678'),
                    'role_id' => 4,
                    'last_activity' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                
                // Prepare facility data
                $facilityData[] = [
                    'user_id' => $userId,
                    'facility_name' => $facility['facility_name'],
                    'phone' => "1900 2115",
                    'address' => $facility["address"],
                    'description' => $this->defaultDescription, // Shortened description
                    'status' => 'pending',
                    'working_time' => "Thứ 2 - Thứ 7: 07:00 - 16:00 | Chủ Nhật: 07:00 - 12:00",
                    'thumbnail' => null,
                    'logo' => $facility['logo'],
                    'website' => null,
                    'lat' => 0,
                    'lng' => 0,
                    'legal_representative_name' => 'Người đại diện ' . ($index + 1),
                    'legal_representative_id' => 'ID' . ($index + 1),
                    'tax_code' => 'TAX' . ($index + 1),
                    'medical_practice_license' => 'LICENSE' . ($index + 1),
                    'issuance_date' => $now,
                    'issuance_place' => 'Cơ quan cấp ' . ($index + 1),
                    'facility_type_id' => vnfaker()->numberBetween(1, 5),
                    'city_id' => vnfaker()->numberBetween(1, 5),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            // Bulk insert facilities
            MedicalFacility::insert($facilityData);
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();
            throw $e;
        }

    }
}
