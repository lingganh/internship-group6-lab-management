<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .info-box {
            background: #fbfcff;
            border: 1px solid #e6eaf2;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e6eaf2;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            min-width: 140px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 500;
        }
        .header {
            background-color: #1376bc;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .content h4 {
            color: #1376bc;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .content p, li {
            color: #666;
            line-height: 1.6;
            font-size: 16px;
        }

        .content .button {
            display: inline-block;
            background-color: #1376bc;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .content .button:hover {
            background-color: #1870ae;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
            color: #888;
            font-size: 12px;
        }

        .footer img {
            max-width: 70px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header section -->
    <div class="header">
        <h1> Tài khoản của bạn đã được phê duyệt</h1>
    </div>

    <!-- Main content section -->
    <div class="content">
        <h4>Xin chào {{ $user->full_name }},</h4>
        <p>Xin chúc mừng. Tài khoản của bạn đã được phê duyệt thành công. Bây giờ bạn có thể truy nhập sử dụng dịch vụ của hệ thống quản lý phòng Lab Phát triển phần mềm và Hệ thống thông minh. </p>

        <div class="info-box">
            <p style="margin: 5px 0;"><strong>Thông tin tài khoản:</strong></p>
            <div class="info-row">
                <div class="info-label">Email đăng nhập:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mã giảng viên:</div>
                <div class="info-value">{{ $user->code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ngày đăng ký:</div>
                <div class="info-value">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>
        </div>


        <a href="{{ route('login')}}" class="button">Đăng nhập ngay</a>
        <p><strong>Lưu ý:</strong></p>
        <ul>
            <li>Để bảo mật, vui lòng không chia sẻ tài khoản này cho người khác.</li>
            <li>Nếu bạn chưa chưa có mật khẩu, Vui lòng bấm quên mật khẩu để thiết lập mật khẩu.</li>
            <li>Nếu bạn có ý kiến vui lòng liên hệ với quản trị viên để được hỗ trợ.</li>
        </ul>
        <p>Nếu nút không hoạt động, sao chép đường link sau và dán vào trình duyệt:</p>
        <p style="word-break:break-all;color:#1a73e8">{{ route('login') }}</p>

        <hr>
        <p style="font-size:16px;color:#666">Trân trọng, <br>Lab Management</p>
    </div>

    <!-- Footer section -->
    <div class="footer">
        <img src="{{ $message->embed(public_path('assets/images/logoST.jpg')) }}">
        <p>Hệ thống quản lý phòng Lab Phát triển phần mềm và Hệ thống thông minh</p>
    </div>
</div>
</body>
</html>
