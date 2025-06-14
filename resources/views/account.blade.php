<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <img src="https://res.cloudinary.com/dftiye2et/image/upload/c_fill,g_auto,h_250,w_970/b_rgb:000000,e_gradient_fade,y_-0.50/c_scale,co_rgb:ffffff,fl_relative,l_text:montserrat_25_style_light_align_center:Shop%20Now,w_0.5,y_0.18/v1740570293/logo_processed_diojsx.png" alt="">
    <h2>Xin chào, B.S {{ $data['name'] }}</h2>
    <p>Phòng khám {{ $data['facility'] }} đã đăng ký cho bạn tài khoản bác sĩ trực thuộc. Dưới đây là thông tin tài khoản dăng nhập</p>
    <p>Họ và tên: {{ $data['name'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    <p>Mật khẩu: {{ $data['password'] }}</p>
</body>
</html>
