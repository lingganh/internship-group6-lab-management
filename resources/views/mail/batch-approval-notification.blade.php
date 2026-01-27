<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch phòng lab đã được phê duyệt</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222;">

    <p>
        Xin chào
        <strong>
            {{ optional($events->first()->user)->full_name
                ?? optional($events->first()->user)->name
                ?? 'bạn' }}
        </strong>,
    </p>

    <p>
        Các lịch đăng ký phòng lab của bạn đã được
        <strong style="color: green;">phê duyệt</strong>.
    </p>

    <p>
        <strong>Số lượng lịch:</strong> {{ $events->count() }}
    </p>

    <table cellpadding="6" cellspacing="0" border="1" width="100%"
           style="border-collapse: collapse; font-size: 14px;">
        <thead style="background: #f3f4f6;">
            <tr>
                <th align="left">Tiêu đề</th>
                <th align="left">Phòng</th>
                <th align="left">Thời gian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->lab_code }}</td>
                    <td>
                        {{ optional($event->start)->format('d/m/Y H:i') }}
                        →
                        {{ optional($event->end)->format('H:i') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 12px;">
        <strong>Mã phòng lab:</strong>
        <span style="font-size: 16px;">{{ $roomCode }}</span>
    </p>

    <p>
        Vui lòng đến đúng giờ và tuân thủ quy định sử dụng phòng lab.
    </p>

    <p style="margin-top: 24px;">
        Trân trọng,<br>
        <strong>Hệ thống quản lý phòng lab</strong>
    </p>

</body>
</html>
