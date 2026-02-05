<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch lặp đã được phê duyệt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .email-header p {
            font-size: 14px;
            opacity: 0.95;
        }

        .email-body {
            padding: 30px 25px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .info-box {
            background: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 16px 18px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .info-box h3 {
            font-size: 15px;
            color: #15803d;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }

        .info-value {
            color: #1f2937;
            font-size: 14px;
            font-weight: 500;
        }

        .code-box {
            background: #fef3c7;
            border: 2px dashed #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }

        .code-label {
            font-size: 13px;
            color: #92400e;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .code-value {
            font-size: 28px;
            font-weight: 700;
            color: #92400e;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }

        .schedule-list {
            background: #f9fafb;
            border-radius: 8px;
            padding: 18px;
            margin: 20px 0;
        }

        .schedule-list h3 {
            font-size: 15px;
            color: #1f2937;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .schedule-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .schedule-item:last-child {
            margin-bottom: 0;
        }

        .schedule-item:hover {
            border-color: #16a34a;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.1);
        }

        .schedule-date {
            font-weight: 600;
            color: #16a34a;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .schedule-time {
            color: #6b7280;
            font-size: 13px;
        }

        .notice-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 18px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .notice-box p {
            font-size: 14px;
            color: #1e40af;
            margin: 5px 0;
        }

        .notice-box strong {
            color: #1e3a8a;
        }

        .footer-note {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }

        .email-footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }

        .email-footer p {
            margin: 5px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .email-header {
                padding: 25px 15px;
            }

            .email-header h1 {
                font-size: 20px;
            }

            .email-body {
                padding: 20px 15px;
            }

            .code-value {
                font-size: 24px;
                letter-spacing: 2px;
            }

            .info-row {
                flex-direction: column;
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>✅ Lịch lặp đã được phê duyệt</h1>
            <p>{{ $eventCount }} buổi sử dụng phòng lab</p>
        </div>

        <div class="email-body">
            <div class="greeting">
                Xin chào <strong>{{ $firstEvent->user?->full_name ?? 'bạn' }}</strong>,
            </div>

            <p style="margin-bottom: 20px; color: #4b5563;">
                Yêu cầu đăng ký lịch lặp sử dụng phòng lab của bạn đã được <strong style="color: #16a34a;">phê duyệt</strong>.
                Dưới đây là thông tin chi tiết:
            </p>

            <div class="info-box">
                <h3>📋 Thông tin chung</h3>
                <div class="info-row">
                    <span class="info-label">Tiêu đề:</span>
                    <span class="info-value">{{ $firstEvent->title }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phòng lab:</span>
                    <span class="info-value">{{ $firstEvent->lab?->name ?? $firstEvent->lab_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mã khóa mở cửa:</span>
                    <span class="info-value">{{ $firstEvent->lab_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Loại:</span>
                    <span class="info-value">
                        @if($firstEvent->category === 'work')
                            Làm việc / Nghiên cứu
                        @elseif($firstEvent->category === 'seminar')
                            Hội thảo / Seminar
                        @else
                            {{ ucfirst($firstEvent->category) }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tổng số buổi:</span>
                    <span class="info-value">{{ $eventCount }} buổi</span>
                </div>
            </div>

            @if($roomCode)
            <div class="code-box">
                <div class="code-label">🔑 Mã mở cửa phòng</div>
                <div class="code-value">{{ $roomCode }}</div>
            </div>
            @endif

            <div class="schedule-list">
                <h3>📅 Danh sách các buổi ({{ $eventCount }} buổi)</h3>
                @foreach($events->sortBy('start') as $event)
                <div class="schedule-item">
                    <div class="schedule-date">
                        Buổi {{ $loop->iteration }}: {{ $event->start->format('d/m/Y') }}
                    </div>
                    <div class="schedule-time">
                        ⏰ {{ $event->start->format('H:i') }} - {{ $event->end->format('H:i') }}
                        @if($event->description)
                            <br>📝 {{ Str::limit($event->description, 60) }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="notice-box">
                <p><strong>📌 Lưu ý quan trọng:</strong></p>
                <p>• Vui lòng đến đúng giờ cho mỗi buổi học</p>
                <p>• Giữ gìn vệ sinh và trang thiết bị phòng lab</p>
                <p>• Báo trước nếu cần hủy bất kỳ buổi nào</p>
                @if($roomCode)
                <p>• Không chia sẻ Mã khóa mở cửa cho người khác</p>
                @endif
            </div>

            @if($firstEvent->feedback)
            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <strong style="color: #92400e;">💬 Ghi chú từ quản trị viên:</strong>
                <p style="margin-top: 8px; color: #78350f;">{{ $firstEvent->feedback }}</p>
            </div>
            @endif

            <div class="footer-note">
                <p><strong>Cần hỗ trợ?</strong></p>
                <p>
                    Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với bộ phận quản lý phòng lab
                    hoặc trả lời email này.
                </p>
            </div>
        </div>

        <div class="email-footer">
            <p><strong>Hệ thống quản lý phòng Lab Phát triển phần mềm và Hệ thống thông minh</strong></p>
            <p>Email này được gửi tự động, vui lòng không trả lời trực tiếp.</p>
            <p style="margin-top: 10px; color: #9ca3af;">
                © {{ date('Y') }} Lab Management System. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
