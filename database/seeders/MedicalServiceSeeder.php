<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalService;

class MedicalServiceSeeder extends Seeder
{
    private function getImgUrl($i) {
        return "https://res.cloudinary.com/dftiye2et/image/upload/v1740473611/medical-service-{$i}";
    }

    public function run(): void
    {
       $services = [
            [
                'name' => 'Nội soi dạ dày',
                'category' => 'Nội soi',
                'duration' => 60,
                'description' => 'Nội soi dạ dày là phương pháp sử dụng ống nội soi mềm đưa qua đường miệng để kiểm tra thực quản, dạ dày và tá tràng. Quy trình diễn ra nhanh chóng, an toàn, giúp phát hiện sớm viêm loét, xuất huyết, polyp, hay ung thư dạ dày. Phù hợp cho người có triệu chứng đau bụng, ợ nóng, khó tiêu, hoặc tiền sử bệnh lý dạ dày.'
            ],
            [
                'name' => 'Tầm soát ung thư vú',
                'category' => 'Tầm soát ung thư',
                'duration' => 45,
                'description' => 'Tầm soát ung thư vú giúp phát hiện sớm các khối u hoặc tổn thương bất thường ở tuyến vú thông qua siêu âm, chụp nhũ ảnh và khám lâm sàng. Phù hợp cho phụ nữ từ 40 tuổi trở lên hoặc có yếu tố di truyền. Việc tầm soát định kỳ giúp tăng khả năng điều trị thành công và giảm thiểu nguy cơ tiến triển nặng.'
            ],
            [
                'name' => 'Khám tổng quát',
                'category' => 'Khám sức khỏe định kỳ',
                'duration' => 90,
                'description' => 'Gói khám sức khỏe tổng quát bao gồm khám lâm sàng, đo huyết áp, BMI, xét nghiệm máu, nước tiểu, siêu âm bụng, chụp X-quang phổi và tư vấn kết quả. Dành cho mọi lứa tuổi để đánh giá tình trạng sức khỏe toàn diện, phát hiện bệnh lý tiềm ẩn và xây dựng lối sống lành mạnh.'
            ],
            [
                'name' => 'Xét nghiệm máu',
                'category' => 'Xét nghiệm',
                'duration' => 30,
                'description' => 'Dịch vụ xét nghiệm máu bao gồm kiểm tra công thức máu, chức năng gan - thận, đường huyết, mỡ máu và các chỉ số sinh học khác. Phù hợp với người kiểm tra sức khỏe định kỳ hoặc theo dõi điều trị bệnh. Giúp phát hiện sớm các bệnh như tiểu đường, thiếu máu, rối loạn chuyển hóa, viêm nhiễm, v.v.'
            ],
            [
                'name' => 'Siêu âm bụng tổng quát',
                'category' => 'Chẩn đoán hình ảnh',
                'duration' => 40,
                'description' => 'Siêu âm bụng tổng quát là phương pháp chẩn đoán không xâm lấn để kiểm tra gan, mật, tụy, thận, bàng quang và hệ tiêu hóa. Giúp phát hiện sỏi, u nang, viêm nhiễm và các bất thường trong ổ bụng. Thực hiện nhanh, không đau, phù hợp với mọi đối tượng, kể cả phụ nữ mang thai.'
            ],
            [
                'name' => 'Khám phụ khoa',
                'category' => 'Sản phụ khoa',
                'duration' => 50,
                'description' => 'Dịch vụ khám phụ khoa bao gồm khám âm đạo, cổ tử cung, siêu âm đầu dò hoặc bụng, xét nghiệm Pap smear và tư vấn sức khỏe sinh sản. Phù hợp với phụ nữ đã quan hệ tình dục hoặc có dấu hiệu bất thường. Giúp phát hiện sớm viêm nhiễm, u xơ tử cung, buồng trứng đa nang, ung thư cổ tử cung, v.v.'
            ],
            [
                'name' => 'Khám tai mũi họng',
                'category' => 'Chuyên khoa',
                'duration' => 30,
                'description' => 'Dịch vụ khám tai mũi họng giúp chẩn đoán các bệnh lý phổ biến như viêm xoang, viêm họng, viêm tai giữa, ù tai, dị ứng mũi và rối loạn giọng nói. Bác sĩ sẽ kiểm tra trực tiếp bằng đèn nội soi và đề xuất phương pháp điều trị phù hợp. Phù hợp cho mọi lứa tuổi, đặc biệt là người có triệu chứng kéo dài.'
            ],
            [
                'name' => 'Tư vấn dinh dưỡng',
                'category' => 'Dinh dưỡng',
                'duration' => 60,
                'description' => 'Buổi tư vấn dinh dưỡng 1-1 với chuyên gia giúp xây dựng chế độ ăn phù hợp với thể trạng, bệnh lý nền (như béo phì, tiểu đường, gout, loãng xương...) và mục tiêu sức khỏe. Hướng dẫn lựa chọn thực phẩm, thời gian ăn, và khẩu phần hợp lý. Phù hợp với mọi lứa tuổi, đặc biệt là người có nhu cầu kiểm soát cân nặng hoặc phục hồi sức khỏe.'
            ],
            [
                'name' => 'Khám tim mạch',
                'category' => 'Tim mạch',
                'duration' => 60,
                'description' => 'Dịch vụ khám tim mạch gồm đo huyết áp, điện tâm đồ (ECG), siêu âm tim, kiểm tra cholesterol, đường huyết và khám lâm sàng bởi bác sĩ chuyên khoa. Giúp phát hiện sớm các bệnh lý như cao huyết áp, thiếu máu cơ tim, rối loạn nhịp tim, suy tim... Đặc biệt phù hợp với người lớn tuổi hoặc có tiền sử bệnh tim.'
            ],
            [
                'name' => 'Khám mắt',
                'category' => 'Mắt',
                'duration' => 30,
                'description' => 'Khám mắt bao gồm đo thị lực, đo khúc xạ (cận, viễn, loạn thị), soi đáy mắt, kiểm tra áp lực mắt và khám cấu trúc giác mạc - võng mạc. Dành cho người có biểu hiện mỏi mắt, nhức mắt, giảm thị lực hoặc cần kiểm tra định kỳ. Giúp phát hiện các bệnh lý như tăng nhãn áp, đục thủy tinh thể, thoái hóa hoàng điểm.'
            ],
            [
                'name' => 'Khám da liễu',
                'category' => 'Chuyên khoa',
                'duration' => 30,
                'description' => 'Dịch vụ khám da liễu giúp phát hiện và điều trị các bệnh lý về da như viêm da cơ địa, mụn trứng cá, nám, sạm da, nấm da, vảy nến... Bác sĩ sẽ tư vấn phác đồ điều trị phù hợp với tình trạng da của từng người.'
            ],
            [
                'name' => 'Tư vấn tâm lý',
                'category' => 'Tâm lý học',
                'duration' => 60,
                'description' => 'Dịch vụ tư vấn tâm lý hỗ trợ giải tỏa căng thẳng, lo âu, trầm cảm, mất ngủ và các rối loạn cảm xúc. Các chuyên gia tâm lý sẽ lắng nghe và giúp khách hàng tìm ra hướng giải quyết phù hợp cho các vấn đề cá nhân và xã hội.'
            ],
            [
                'name' => 'Đo loãng xương',
                'category' => 'Chẩn đoán hình ảnh',
                'duration' => 20,
                'description' => 'Dịch vụ đo mật độ xương (DEXA scan) giúp đánh giá nguy cơ loãng xương và gãy xương, đặc biệt ở người cao tuổi hoặc phụ nữ sau mãn kinh. Phát hiện sớm để có biện pháp can thiệp kịp thời.'
            ],
            [
                'name' => 'Khám nhi khoa',
                'category' => 'Nhi khoa',
                'duration' => 45,
                'description' => 'Khám sức khỏe tổng quát cho trẻ nhỏ nhằm theo dõi sự phát triển thể chất, tinh thần và phát hiện sớm các bệnh lý phổ biến ở trẻ như viêm họng, tiêu chảy, nhiễm trùng tai giữa... Dịch vụ phù hợp cho trẻ từ sơ sinh đến 15 tuổi.'
            ],
            [
                'name' => 'Tiêm chủng ngừa',
                'category' => 'Dự phòng',
                'duration' => 25,
                'description' => 'Dịch vụ tiêm chủng với các loại vắc xin phòng ngừa bệnh truyền nhiễm như viêm gan B, sởi, rubella, cúm mùa, uốn ván... Đảm bảo an toàn, đúng lịch và được tư vấn kỹ càng trước khi tiêm.'
            ]
        ];
        for ($i = 0; $i < 5; $i++) {
            foreach ($services as $index => $service) {
            $medicalService = MedicalService::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'thumbnail' => $this->getImgUrl(rand(1,5)),
                'category' => $service['category'],
                'price' => rand(300, 1000) * 1000,
                'duration' => $service['duration'],
                'status' => 'active',
                'service_audience_gender' => ['male', 'female', 'both'][rand(0, 2)],
                'is_public' => $index >= 5,
                'min_age_requirement' => rand(0, 18),
                'max_age_requirement' => rand(50, 80),
                'medical_facility_id' => $i + 1,
            ]);
            // Get all doctor IDs for the medical facility
            $doctorIds = $medicalService->medicalFacility->doctors()->pluck('id')->toArray();
            // Attach doctors to the medical service
            $medicalService->doctors()->attach($doctorIds);
            }
        }
    }
}
