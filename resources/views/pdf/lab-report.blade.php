<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
</head>

<body>

    {{-- ================= HEADER  ================= --}}
    <div style="position: relative; margin-bottom: 20px;">

        <!-- LOGO (RIGHT) -->
        <img src="{{ public_path('assets/images/FITA.png') }}"
            style="
            position: absolute;
            right: 0;
            top: 0;
            width: 90px;
            height: auto;
        ">

        <!-- CENTER TITLE -->
        <div style="text-align:center;">
            <h1 style="margin:0;">HỌC VIỆN NÔNG NGHIỆP VIỆT NAM</h1>
            <h1 style="margin:0;">KHOA CÔNG NGHỆ THÔNG TIN</h1>
            <h2 style="margin:0;">BÁO CÁO SỬ DỤNG PHÒNG LAB</h2>

            <p style="margin:4px 0; font-size:13px;">
                Thời gian:
                {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d/m/Y') : '---' }}
                →
                {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d/m/Y') : '---' }}
            </p>
            <p style="margin:4px 0; font-size:13px;">
                @if($lab=='all')

                @else
                {{$lab}}
                @endif
            </p>

        </div>

    </div>

    {{-- ================= SUMMARY PAGE ================= --}}
    <div class="summary">
        <div class="summary-title">I. TỔNG QUAN</div>

        <table class="summary-table">
            <tr>
                <th>Tổng sự kiện</th>
                <th>Đã duyệt</th>
                <th>Chờ duyệt</th>
                <th>Từ chối</th>
                <th>Hoàn thành</th>
            </tr>
            <tr>
                <td class="summary-badge">{{ $summary['total'] }}</td>
                <td class="summary-badge s-approved">{{ $summary['approved'] }}</td>
                <td class="summary-badge s-pending">{{ $summary['pending'] }}</td>
                <td class="summary-badge s-cancelled">{{ $summary['cancelled']}}</td>
                <td class="summary-badge s-completed">{{ $summary['completed'] }}</td>

            </tr>
        </table>
    </div>

    <hr>

    {{-- ================= EVENTS GROUP BY LAB ================= --}}
    <h3 style="font-size:16px; margin-bottom:10px;">
        II. DANH SÁCH SỰ KIỆN THEO PHÒNG LAB
    </h3>

    @foreach($groupedEvents as $labCode => $labEvents)

    {{-- Page break cho mỗi Lab (trừ lab đầu) --}}
    <div class="lab-section" style="{{ !$loop->first ? 'page-break-before: always;' : '' }}">

        {{-- LAB TITLE --}}
        <div class="lab-title">
            Phòng Lab: {{ $labCode }}
        </div>

        {{-- MINI SUMMARY --}}
        @php
        $total = $labEvents->count();
        $approved = $labEvents->where('status','approved')->count();
        $pending = $labEvents->where('status','pending')->count();
        $completed = $labEvents->where('status','completed')->count();
        $cancelled=$labEvents->where('status','cancelled')->count();
        @endphp

        <div class="lab-summary">
            <span class="status status-approved">Đã duyệt: {{ $approved }}</span>
            <span class="status status-pending">Chờ duyệt: {{ $pending }}</span>
            <span class="status status-completed">Hoàn thành: {{ $completed }}</span>
            <span class="status status-cancelled">Từ chối: {{ $cancelled }}</span>
            <span><strong>Tổng:</strong> {{ $total }}</span>
        </div>

        {{-- EVENT TABLE --}}
        <table class="event-table">
            <thead>
                <tr>
                    <th width="18%">Tiêu đề</th>
                    <th width="12%">Loại</th>
                    <th width="28%">Thời gian</th>
                    <th width="12%">Trạng thái</th>
                    <th width="30%">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @php
                $statusMap = [
                'approved' => 'Đã duyệt',
                'pending' => 'Chờ duyệt',
                'completed' => 'Hoàn thành',
                'cancelled'=> 'Từ chối'
                ];

                $categoryMap=[
                'work'=>'Làm việc-Nghiên cứu',
                'seminar'=>'Hội thảo-Seminar',
                'other'=>'Khác'
                ];
                @endphp

                @foreach($labEvents as $event)
                <tr>
                    <td>{{ $event->title }} </td>
                    <td>{{  $categoryMap[$event->category] ?? 'Không xác định' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($event->start)->format('d/m/Y H:i') }}
                        <br>
                        → {{ \Carbon\Carbon::parse($event->end)->format('d/m/Y H:i') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="status status-{{ $event->status }}">
                            {{ $statusMap[$event->status] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>{{ $event->description ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    @endforeach


    {{-- ================= EQUIPMENT SUMMARY ================= --}}
    <div class="page-break"></div>

    <div class="section-title">
        TỔNG QUAN THIẾT BỊ
    </div>

    {{-- STATISTICS --}}
    <div class="stats-box">
        <div class="stat stat-total">
            <strong>{{ $equipmentStats['total'] }}</strong>
            Tổng thiết bị
        </div>
        <div class="stat stat-available">
            <strong>{{ $equipmentStats['available'] }}</strong>
            Có thể sử dụng
        </div>
        <div class="stat stat-maintenance">
            <strong>{{ $equipmentStats['maintenance'] }}</strong>
            Đang sửa chữa
        </div>
        <div class="stat stat-broken">
            <strong>{{ $equipmentStats['broken'] }}</strong>
            Đã Hỏng
        </div>
    </div>

    {{-- GROUP BY TYPE --}}
    @foreach($equipmentsByType as $type => $items)

    <div class="section-title">
        {{ strtoupper($type) }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:20%">Tên thiết bị</th>
                <th style="width:10%">Lab id</th>
                <th style="width:15%">Mã</th>
                <th style="width:10%">Số lượng thực tế</th>
                <th style="width:10%">Số lượng bị hỏng</th>
                <th style="width:30%">Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $eq)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $eq['name'] }}</td>
                <td>{{$eq['lab_name']}}</td>
                <td>{{ $eq['code'] }}</td>
                <td>{{$eq['actual_quantity']}}</td>
                <td>{{$eq['broken_quantity']}}</td>

                <td>{{ $eq['notes'] ?? '—' }}</td>
            </tr>
            @endforeach

        </tbody>

    </table>
    @endforeach

</body>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 13px;
        color: #333;
    }

    .report-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .report-meta {
        text-align: center;
        font-size: 11px;
        color: #666;
        margin-bottom: 15px;
    }

    hr {
        border: none;
        border-top: 1px solid #ddd;
        margin: 10px 0 20px;
    }

    .event-card {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 15px;
        page-break-inside: avoid;
    }

    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .event-title {
        font-weight: bold;
        font-size: 15px;
    }

    .badge {
        padding: 4px 10px;
        font-size: 11px;
        border-radius: 12px;
        color: #fff;
    }

    .approved {
        background: #2e7d32;
    }

    .pending {
        background: #f9a825;
    }

    .completed {
        background: #1565c0;
    }

    .rejected {
        background: #c62828;
    }

    .event-body {
        display: table;
        width: 100%;
    }

    .col {
        display: table-cell;
        width: 50%;
        vertical-align: top;
    }

    .row {
        margin-bottom: 6px;
    }

    .label {
        font-weight: bold;
        width: 120px;
        display: inline-block;
    }

    .muted {
        color: #888;
        font-style: italic;
    }

    .event-footer {
        margin-top: 10px;
        font-size: 11px;
        color: #777;
        border-top: 1px dashed #ddd;
        padding-top: 6px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 13px;
        color: #333;
    }

    .report-header {
        margin-bottom: 25px;
    }

    .header-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .header-subtitle {
        text-align: center;
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    .header-divider {
        border-top: 2px solid #333;
        margin: 10px 0 12px;
    }

    .header-meta {
        font-size: 11px;
        line-height: 1.6;
    }

    .summary {
        margin-bottom: 30px;
    }

    .summary-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-table th,
    .summary-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        font-size: 13px;
    }

    .summary-table th {
        background: #f5f5f5;
        font-weight: bold;
    }

    .summary-badge {
        font-weight: bold;
    }

    .s-approved {
        color: #2e7d32;
    }

    .s-pending {
        color: #f9a825;
    }

    .s-completed {
        color: #1565c0;
    }

    .s-cancelled {
        color: #c62828
    }

    .lab-section {
        margin-top: 25px;
    }

    .lab-title {
        font-size: 15px;
        font-weight: bold;
        padding: 6px 10px;
        background: #eeeeee;
        border-left: 5px solid #1565c0;
        margin-bottom: 8px;
    }

    .event-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .event-table th,
    .event-table td {
        border: 1px solid #ddd;
        padding: 6px;
    }

    .event-table th {
        background: #f7f7f7;
        font-weight: bold;
        text-align: center;
    }

    .event-table td {
        vertical-align: top;
    }

    .status {
        font-weight: bold;
        text-transform: capitalize;
    }

    .status-approved {
        color: #2e7d32;
        /* xanh lá */
    }

    .status-pending {
        color: #f9a825;
        /* vàng */
    }

    .status-completed {
        color: #1565c0;
        /* xanh dương */
    }

    .status-cancelled{
        color: #c62828;
        /* đỏ (nếu có) */
    }

    @page {
        margin: 30px 40px 50px 40px;
    }

    footer {
        position: fixed;
        bottom: -25px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 10px;
        color: #777;
    }

    .lab-summary {
        margin-bottom: 8px;
        font-size: 12px;
    }

    .lab-summary span {
        margin-right: 15px;
        font-weight: bold;
    }

    .page-break {
        page-break-before: always;
    }

    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 10px;
        border-left: 4px solid #333;
        padding-left: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: left;
    }

    th {
        background: #f3f3f3;
    }

    .status-available {
        color: #2e7d32;
        font-weight: bold;
    }

    .status-maintenance {
        color: #f9a825;
        font-weight: bold;
    }

    .status-broken {
        color: #c62828;
        font-weight: bold;
    }

    .stats-box {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }

    .stat {
        display: table-cell;
        width: 25%;
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 11px;
    }

    .stat strong {
        font-size: 18px;
        display: block;
    }

    .stat-total {
        background: #f5f5f5;
    }

    .stat-available {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .stat-maintenance {
        background: #fff8e1;
        color: #f9a825;
    }

    .stat-broken {
        background: #fdecea;
        color: #c62828;
    }

    .page-break {
        page-break-before: always;
    }

    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin: 10px 0;
        border-left: 4px solid #333;
        padding-left: 8px;
    }



    .status-available {
        color: #2e7d32;
        font-weight: bold;
    }

    .status-maintenance {
        color: #f9a825;
        font-weight: bold;
    }

    .status-broken {
        color: #c62828;
        font-weight: bold;
    }
</style>


</html>