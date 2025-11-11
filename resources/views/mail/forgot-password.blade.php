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
        <h1> Quên mật khẩu </h1>
    </div>

    <!-- Main content section -->
    <div class="content">
        <h4>Xin chào {{ $user->full_name }},</h4>
        <p>Bạn đã gửi yêu cầu lấy lại mật khẩu tài khoản trên hệ thống Quản lý phòng Lab, vui lòng nhấn vào nút dưới đây để xác minh email và đặt lại mật khẩu: </p>
        <a href="{{ route('setPassword', ['token' => $token]) }}" class="button">Xác minh email</a>
        <p><strong>Lưu ý:</strong></p>
        <ul>
            <li>Link này có hiệu lực trong <strong>60 phút</strong>.</li>
            <li>Hết hạn lúc: <strong>{{ now()->addMinutes(60)->format('H:i d/m/Y') }}</strong>.</li>
            <li>Nếu bạn không yêu cầu, vui lòng bỏ qua email này.</li>
            <li>Nếu bạn có ý kiến vui lòng liên hệ với quản trị viên để được hỗ trợ.</li>
        </ul>
        <p>Nếu nút không hoạt động, sao chép đường link sau và dán vào trình duyệt:</p>
        <p style="word-break:break-all;color:#1a73e8">{{ route('setPassword', ['token' => $token]) }}</p>

        <hr>
        <p style="font-size:16px;color:#666">Trân trọng, <br>Lab Management</p>
    </div>

    <!-- Footer section -->
    <div class="footer">
        <img src="{{ $message->embed(public_path('assets/images/logoST.jpg')) }}">
        <p>Hệ thống quản lý phòng Lab 304</p>
    </div>
</div>
</body>
</html>
