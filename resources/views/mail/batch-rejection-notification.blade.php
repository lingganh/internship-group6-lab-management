<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo từ chối lịch</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="margin: 0; font-size: 24px;">❌ Thông Báo Từ Chối Lịch</h1>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Xin chào <strong>{{ $events->first()->user->full_name }}</strong>,
        </p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            Chúng tôi rất tiếc phải thông báo rằng <strong style="color: #dc2626;">{{ $count }} lịch đăng ký</strong> của bạn đã bị từ chối.
        </p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc2626;">
            <h3 style="margin-top: 0; color: #dc2626;">Lý do từ chối:</h3>
            <p style="margin: 0; font-style: italic;">
                {{ $reason ?: 'Không có lý do cụ thể' }}
            </p>
        </div>

        <h3 style="color: #333; margin-top: 30px; margin-bottom: 15px;">Danh sách lịch bị từ chối:</h3>
        
        <div style="background: white; padding: 15px; border-radius: 8px;">
            @foreach($events as $event)
                <div style="padding: 15px; margin-bottom: 10px; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <div style="font-weight: bold; color: #111827; margin-bottom: 8px;">
                        {{ $event->title }}
                    </div>
                    <div style="font-size: 14px; color: #6b7280;">
                        <div style="margin-bottom: 4px;">
                            <strong>Phòng:</strong> {{ $event->lab?->name }} ({{ $event->lab_code }})
                        </div>
                        <div style="margin-bottom: 4px;">
                            <strong>Thời gian:</strong> {{ $event->start->format('H:i d/m/Y') }} - {{ $event->end->format('H:i d/m/Y') }}
                        </div>
                        @if($event->description)
                            <div style="margin-top: 8px; font-style: italic;">
                                {{ $event->description }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 20px; border-radius: 6px;">
            <p style="margin: 0; font-size: 14px;">
                💡 <strong>Lưu ý:</strong> Bạn có thể đăng ký lại hoặc liên hệ với ban quản lý để được hỗ trợ thêm.
            </p>
        </div>

        

        <p style="font-size: 14px; color: #6b7280; margin-top: 30px; text-align: center;">
            Nếu có thắc mắc, vui lòng liên hệ ban quản lý.
        </p>
    </div>

    <div style="text-align: center; margin-top: 20px; color: #9ca3af; font-size: 12px;">
        <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        <p>© {{ date('Y') }} Lab Management System. All rights reserved.</p>
    </div>
</body>
</html>