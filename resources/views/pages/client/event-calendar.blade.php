<x-client-layout>
  <style>
    :root {
      --primary: #2563eb;
      --bg-main: #ffffff;
      --bg-sub: #f8fafc;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #f1f5f9;
      --radius: 12px;
    }

    .seminar-wrap { 
      max-width: 1240px;
      margin: 0 auto;
      padding: 40px 20px; 
      font-family: 'Inter', system-ui, sans-serif;
      color: var(--text-main);
    }

    /* --- Header & Search --- */
    .seminar-top { 
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      margin-bottom: 30px; 
      gap: 20px;
      flex-wrap: wrap;
    }
    
    .seminar-title h4 { 
      font-size: 26px; font-weight: 800; margin: 0; 
      letter-spacing: -0.03em; color: #1e293b;
    }
    
    .seminar-filters { display: flex; gap: 10px; flex-grow: 1; justify-content: flex-end; }
    
    .seminar-control input, .seminar-control select {
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 15px;
      font-size: 14px;
      background: var(--bg-sub);
      outline: none;
      transition: all 0.2s;
      min-width: 200px;
    }
    
    .seminar-control input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.05); }

    /* --- Table Design --- */
    .table-responsive {
      background: var(--bg-main);
      border-radius: var(--radius);
      border: 1px solid var(--border-color);
      overflow: hidden; /* Tránh tràn border radius */
      min-height: 400px; /* Giữ kích thước kể cả khi trống */
    }

    .custom-table { 
      width: 100%; 
      border-collapse: collapse; 
      table-layout: fixed; /* Cố định chiều rộng cột */
    }

    /* Định nghĩa chiều rộng cố định cho các cột trên Desktop */
    .col-stt { width: 60px; }
    .col-time { width: 220px; }
    .col-event { width: auto; }
    .col-lab { width: 180px; }
    .col-user { width: 180px; }
    .col-status { width: 130px; }

    .custom-table th {
      background: #fcfcfd;
      padding: 16px 20px;
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 1px solid var(--border-color);
      text-align: left;
    }

    .custom-table td {
      padding: 18px 20px;
      font-size: 14px;
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
      word-wrap: break-word; /* Tránh text dài phá vỡ layout */
    }

    .custom-table tbody tr:hover { background-color: #f8fafc; cursor: pointer; }

    /* --- DateTime & Badges --- */
    .datetime-block { display: flex; align-items: center; gap: 12px; }
    .date-icon-box {
      background: #eff6ff; color: var(--primary);
      width: 46px; height: 46px; border-radius: 10px;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      font-weight: 800; line-height: 1.1; flex-shrink: 0;
    }
    .date-icon-box span { font-size: 10px; font-weight: 600; text-transform: uppercase; opacity: 0.8; }
    .time-info { display: flex; flex-direction: column; gap: 2px; }
    .time-val { font-weight: 600; color: var(--text-main); font-size: 14px; white-space: nowrap; }
    .event-cat-text {
  font-size: 12px;
  font-weight: 500;
  color: var(--text-muted); /* Màu xám nhẹ mặc định */
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 2px 0;
  text-transform: lowercase; /* Chữ thường tạo cảm giác nhẹ nhàng hơn */
  font-variant: small-caps; /* Hoặc dùng kiểu chữ này để trông chuyên nghiệp */
}

/* Tạo dấu chấm nhỏ phía trước để phân tách */
.event-cat-text::before {
  content: "•";
  color: var(--primary);
  font-size: 16px;
  line-height: 0;
}

/* Nếu bạn muốn mỗi loại có một màu nhẹ riêng biệt (tùy chọn) */
.cat-work { color: #6366f1; }    /* Indigo */
.cat-seminar { color: #2563eb; } /* Blue */
.cat-other { color: #64748b; }   /* Slate */
    .badge-status {
      font-size: 11px; font-weight: 700; padding: 6px 14px;
      border-radius: 12px; display: inline-block; white-space: nowrap;
    }
    .chip-today { color: #e11d48; background: #fff1f2; }
    .chip-tom { color: #d97706; background: #fff7ed; }
    .chip-up { color: #16a34a; background: #f0fdf4; }

    /* --- Responsive Mobile (Dưới 768px) --- */
    @media (max-width: 768px) {
      .seminar-top { flex-direction: column; align-items: flex-start; }
      .seminar-filters { width: 100%; flex-direction: column; }
      .seminar-control input, .seminar-control select { width: 100%; }

      /* Biến bảng thành dạng thẻ (Card) */
      .custom-table thead { display: none; } /* Ẩn tiêu đề bảng */
      .custom-table, .custom-table tbody, .custom-table tr, .custom-table td {
        display: block;
        width: 100%;
      }
      .custom-table tr {
        margin-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
        padding: 10px 0;
      }
      .custom-table td {
        border: none;
        padding: 8px 20px;
        text-align: left !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .custom-table td::before {
        content: attr(data-label); /* Hiển thị nhãn cột */
        font-weight: 700;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
      }
      .stt-col { display: none !important; }
    }
  </style>

  <div class="seminar-wrap">
    <div class="seminar-top">
      <div class="seminar-title">
        <h3>Sự kiện sắp diễn ra</h3>
      </div>

      <form action="{{ url()->current() }}" method="GET" class="seminar-filters">
        <div class="seminar-control">
          <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm...">
        </div>
        <div class="seminar-control">
          <select name="category" onchange="this.form.submit()">
            <option value="">Tất cả danh mục</option>
            @foreach(['work' => 'Làm việc / Nghiên cứu', 'seminar' => 'Hội thảo / Seminar', 'other' => 'Khác'] as $k => $v)
              <option value="{{ $k }}" {{ request('category') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
      </form>
    </div>

    <div class="table-responsive shadow-sm">
      <table class="custom-table">
        <thead>
          <tr>
            <th class="col-stt">STT</th>
            <th class="col-time">Thời gian & Ngày</th>
            <th class="col-lab">Sự kiện</th>
            <th class="col-event">Phòng Lab</th>
            <th class="col-user">Người phụ trách</th>
            <th class="col-status" style="text-align: center;">Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          @forelse($upcomingEvents as $index => $event)
            @php
              $isToday = $event->start->isToday();
              $isTomorrow = $event->start->isTomorrow();
              $badgeClass = $isToday ? 'chip-today' : ($isTomorrow ? 'chip-tom' : 'chip-up');
              $badgeText = $isToday ? 'Hôm nay' : ($isTomorrow ? 'Ngày mai' : 'Sắp tới');
              $categoryMap = ['work' => 'Nghiên cứu', 'seminar' => 'Seminar', 'other' => 'Khác'];
              $labName = $event->lab->name ?? ($event->lab->code ?? 'N/A');
            @endphp
            <tr class="js-open-event" data-bs-toggle="modal" data-bs-target="#eventDetailModal">
              <td class="stt-col" data-label="STT">{{ $index + 1 }}</td>
              <td data-label="Thời gian">
                <div class="datetime-block">
                  <div class="date-icon-box">
                    {{ $event->start->format('d') }}
                    <span>T{{ $event->start->format('m') }}</span>
                  </div>
                  <div class="time-info">
                    <div class="time-val">{{ $event->start->format('H:i') }} @if($event->end)— {{ $event->end->format('H:i') }}@endif</div>
                    <div class="day-val">{{ $event->start->isoFormat('dddd') }}</div>
                  </div>
                </div>
              </td>
              <td data-label="Sự kiện">
                <span class="event-title-text">{{ $event->title }}</span>
                <br>
                <span class="event-cat-text">{{ $categoryMap[$event->category] ?? '' }}</span>
              </td>
              <td data-label="Phòng Lab">
                <div style="font-weight: 500; font-size: 13px;">
                  <i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $labName }}
                </div>
              </td>
              <td data-label="Phụ trách">
                <span class="user-name">{{ $event->user->full_name ?? $event->user->name ?? 'N/A' }}</span>
              </td>
              <td align="center" data-label="Trạng thái">
                <span class="badge-status {{ $badgeClass }}">{{ $badgeText }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted" style="height: 300px;">
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="fa-regular fa-calendar-xmark mb-3" style="font-size: 40px; opacity: 0.3;"></i>
                    <p class="mb-0">Hiện tại chưa có sự kiện nào được lên lịch.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-client-layout>