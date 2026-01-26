<p><strong>{{ $senderName }}</strong> đã hủy một lịch đặt phòng.</p>

<ul>
    <li><strong>Tiêu đề:</strong> {{ $event->title }}</li>
    <li><strong>Phòng:</strong> {{ $event->lab_code }}</li>
    <li><strong>Thời gian:</strong>
        {{ \Carbon\Carbon::parse($event->start)->format('d/m/Y H:i') }}
        -
        {{ \Carbon\Carbon::parse($event->end)->format('d/m/Y H:i') }}
    </li>
</ul>
<p>Vui lòng kiểm tra hệ thống để biết thêm chi tiết.</p>