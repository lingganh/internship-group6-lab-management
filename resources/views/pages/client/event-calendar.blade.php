<x-client-layout>
 <div class="seminar-wrap">
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
    
    .seminar-title h3 { 
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
      overflow: hidden;
      min-height: 400px;
    }

    .custom-table { 
      width: 100%; 
      border-collapse: collapse; 
      table-layout: fixed;
    }

    .col-stt { width: 60px; }
    .col-time { width: 220px; }
    .col-event { width: auto; }
    .col-cat { width: 220px; } /* Cột loại sự kiện */
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
      word-wrap: break-word;
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
    
    /* .event-cat-text {
      font-size: 12px;
      font-weight: 500;
      color: var(--text-muted);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-transform: lowercase;
      font-variant: small-caps;
    }

    .event-cat-text::before {
      content: "•";
      color: var(--primary);
      font-size: 16px;
      line-height: 0;
    } */

    .badge-status {
      font-size: 11px; font-weight: 700; padding: 6px 14px;
      border-radius: 12px; display: inline-block; white-space: nowrap;
    }
    .chip-today { color: #e11d48; background: #fff1f2; }
    .chip-tom { color: #d97706; background: #fff7ed; }
    .chip-up { color: #16a34a; background: #f0fdf4; }

    .pagination-wrap {
      padding: 15px 20px;
      background: #fcfcfd;
      border-top: 1px solid var(--border-color);
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
      .seminar-top { flex-direction: column; align-items: flex-start; }
      .seminar-filters { width: 100%; flex-direction: column; }
      .seminar-control input, .seminar-control select { width: 100%; }
      .custom-table thead { display: none; }
      .custom-table tr { display: block; margin-bottom: 15px; border-bottom: 2px solid var(--border-color); }
      .custom-table td { display: flex; justify-content: space-between; align-items: center; border: none; padding: 8px 20px; }
      .custom-table td::before { content: attr(data-label); font-weight: 700; color: var(--text-muted); font-size: 12px; text-transform: uppercase; }
      .stt-col { display: none !important; }
    }
  </style>

  <div class="seminar-top">
    <div class="seminar-title">
      <h3>Sự kiện sắp diễn ra</h3>
    </div>

    <div class="seminar-filters">
      <div class="seminar-control">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm kiếm...">
      </div>
      <div class="seminar-control">
        <select wire:model.live="category">
          <option value="">Tất cả danh mục</option>
          <option value="work">Làm việc / Nghiên cứu</option>
          <option value="seminar">Hội thảo / Seminar</option>
          <option value="other">Khác</option>
        </select>
      </div>
      {{-- <div class="seminar-control">
        <select wire:model.live="year">
          <option value="">Tất cả năm</option>
          @foreach($years as $y)
            <option value="{{ $y }}">{{ $y }}</option>
          @endforeach
        </select>
      </div> --}}
    </div>
  </div>

  <div class="table-responsive shadow-sm">
    <table class="custom-table" wire:loading.class="opacity-50">
      <thead>
        <tr>
          <th class="col-stt">STT</th>
          <th class="col-time">Thời gian & Ngày</th>
          <th class="col-event">Sự kiện</th>
          <th class="col-cat">Loại sự kiện</th> <th class="col-user">Người phụ trách</th>
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
            $categoryMap = ['work' => 'Làm việc / Nghiên cứu', 'seminar' => 'Hội Thảo / Seminar', 'other' => 'Khác'];
          @endphp
          <tr wire:key="event-{{ $event->id }}" class="js-open-event" data-bs-toggle="modal" data-bs-target="#eventDetailModal">
            <td class="stt-col" data-label="STT">{{ $index + 1 }}</td>
            <td data-label="Thời gian">
              <div class="datetime-block">
                <div class="date-icon-box">
                  {{ $event->start->format('d') }}
                  <span>T{{ $event->start->format('m') }}</span>
                </div>
                <div class="time-info">
                  <div class="time-val">{{ $event->start->format('H:i') }} @if($event->end)— {{ $event->end->format('H:i') }}@endif</div>
                  <div class="day-val" style="font-size: 12px; color: var(--text-muted);">{{ $event->start->isoFormat('dddd') }}</div>
                </div>
              </div>
            </td>
            <td data-label="Sự kiện">
              <span class="event-title-text" style="font-weight: 600;">{{ $event->title }}</span>
            </td>
            <td data-label="Loại sự kiện">
              <span class="event-cat-text">{{ $categoryMap[$event->category] ?? 'Khác' }}</span>
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
            <td colspan="6" class="text-center py-5 text-muted">
               Hiện tại chưa có sự kiện nào.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

     @if(method_exists($upcomingEvents, 'links'))
    <div class="pagination-wrap">
      {{ $upcomingEvents->links() }}
    </div>
    @endif
  </div>
</div>
</x-client-layout>