<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f7f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e1e1e1; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="background-color: #4e73df; padding: 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 20px; text-transform: uppercase;">Yêu cầu phê duyệt lịch</h1>
        </div>
        <div style="padding: 30px;">
            <p style="font-size: 16px;">Chào <strong>Quản trị viên</strong>,</p>
            <p>Hệ thống vừa nhận được một yêu cầu đăng ký phòng Lab từ <strong>{{ $senderName }}</strong>.</p>

            <div style="background-color: #f8f9fc; border-left: 4px solid #4e73df; padding: 15px; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Sự kiện:</strong> {{ $event->title }}</p>
                <p style="margin: 5px 0;"><strong>Phòng:</strong> <span style="color: #e74a3b; font-weight: bold;">{{ $event->lab_code }}</span></p>
                <p style="margin: 5px 0;"><strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($event->start)->format('H:i d/m/Y') }}</p>
                <p style="margin: 5px 0;"><strong>Đến:</strong> {{ \Carbon\Carbon::parse($event->end)->format('H:i d/m/Y') }}</p>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('admin.approval') }}"
                   style="background-color: #4e73df; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                   Đến trang phê duyệt ngay
                </a>
            </div>
        </div>
        <div style="background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #777;">
            <p style="margin: 0;">Đây là email tự động từ hệ thống Quản lý phòng Lab Phát triển phần mềm và Hệ thống thông minh.</p>
        </div>
    </div>
</body>
</html>
