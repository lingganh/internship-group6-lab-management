<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch đăng ký bị từ chối</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:12px; padding:24px;">
        
        <h2 style="color:#dc2626; margin-bottom:12px;">Lịch đã bị từ chối</h2>

        <p>Xin chào {{ $schedule->user->full_name }},</p>

        <p>Rất tiếc, lịch đăng ký phòng lab của bạn đã bị từ chối.</p>

        <div style="padding:12px 16px; background:#fff0f0; border-radius:8px; margin-bottom:18px;">
            <p><strong>Tiêu đề:</strong> {{ $schedule->title }}</p>
            <p><strong>Phòng:</strong> {{ $schedule->lab?->name ?? $schedule->lab_code }}</p>
            <p><strong>Thời gian:</strong> {{ $schedule->start->format('H:i d/m/Y') }} - {{ $schedule->end->format('H:i d/m/Y') }}</p>
            <p><strong>Lý do từ chối:</strong></p>
            <div style="padding:8px 12px; background:white; border-left:4px solid #dc2626;">
                {{ $reason }}
            </div>
        </div>

       <div class="footer">
           <img src="{{ $message->embed(public_path('assets/images/logoST.jpg')) }}">
           <p>Hệ thống quản lý phòng Lab 304</p>
        </div>
    </div>
</body>
</html>
