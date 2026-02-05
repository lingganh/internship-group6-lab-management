<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f6f8fc;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #16a34a;
        }
        .header h1 {
            color: #16a34a;
            margin: 0;
            font-size: 24px;
        }
        .badge {
            display: inline-block;
            background: #e9f9ef;
            color: #0f6a2e;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }
        .content {
            margin: 20px 0;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
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
        .password-box {
            /*background: linear-gradient(135deg, #16a34a 0%, #0f6a2e 100%);*/
            background-color: #16a34a;
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .password-label {
            font-size: 14px;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        .password-value {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e6eaf2;
            font-size: 14px;
            color: #64748b;
            text-align: center;
        }
        .footer img {
            max-width: 70px;
            margin-top: 10px;
        }
        .note {
            background: #fff3db;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Phê duyệt thành công</h1>
            <span class="badge">Lịch đã được xác nhận</span>
        </div>

        <div class="content">
            <div class="greeting">
                Xin chào <strong>{{ $schedule->user->full_name }}</strong>,
            </div>

            <p>Yêu cầu đăng ký phòng lab của bạn đã được phê duyệt. Dưới đây là thông tin chi tiết:</p>

            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Tiêu đề:</div>
                    <div class="info-value">{{ $schedule->title }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phòng lab:</div>
                    <div class="info-value">{{ $schedule->lab->name ?? $schedule->lab_code }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Thời gian bắt đầu:</div>
                    <div class="info-value">{{ $schedule->start->format('H:i - d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Thời gian kết thúc:</div>
                    <div class="info-value">{{ $schedule->end->format('H:i - d/m/Y') }}</div>
                </div>
            </div>

            <div class="password-box">
                <div class="password-label">🔑 MẬT KHẨU PHÒNG</div>
                <div class="password-value">{{ $password }}</div>
            </div>

            <div class="note">
                <strong> Lưu ý quan trọng:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Vui lòng sử dụng mật khẩu trên để mở cửa phòng lab</li>
                    <li>Không chia sẻ mật khẩu cho người khác</li>
                    <li>Đến đúng giờ và tuân thủ quy định sử dụng phòng lab</li>
                </ul>
            </div>

            <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với bộ phận quản lý.</p>
        </div>

       <div class="footer">
           <img src="{{ $message->embed(public_path('assets/images/logoST.jpg')) }}">
           <p>Hệ thống quản lý phòng Lab Phát triển phần mềm và Hệ thống thông minh</p>
        </div>
    </div>
</body>
</html>
