<x-client-layout>
  <style>
    :root {
      --primary: #6366f1;
      --primary-light: #818cf8;
      --primary-soft: #f5f3ff;
      --bg-main: #f8fafc;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --card-bg: #ffffff;
      --border-color: #f1f5f9;
      --radius-xl: 24px;
      --radius-lg: 16px;
      --radius-md: 12px;
      
      /* Status Colors */
      --today-bg: #fef2f2; --today-text: #ef4444;
      --tom-bg: #fff7ed; --tom-text: #f59e0b;
      --up-bg: #f0fdf4; --up-text: #22c55e;
    }

    .seminar-wrap { 
      width: 100%; padding: 40px 20px; 
      background-color: var(--bg-main);
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: var(--text-main);
      box-sizing: border-box;
    }

    /* --- Header Section --- */
    .seminar-top { 
      display: flex; align-items: center; justify-content: space-between; 
      margin-bottom: 32px; gap: 20px; flex-wrap: wrap;
    }
    .seminar-title h4 { 
      font-size: 28px; font-weight: 800; margin: 0; color: #0f172a;
      letter-spacing: -0.03em;
    }
    
    .seminar-filters { display: flex; gap: 12px; flex-wrap: wrap; width: 100%; max-width: 600px; }
    .seminar-control { position: relative; flex: 1; min-width: 200px; }
    .seminar-control .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); font-size: 14px; pointer-events: none;
    }
    .seminar-control input, .seminar-control select {
      width: 100%; border: 1px solid #e2e8f0; background: #fff;
      border-radius: var(--radius-md); padding: 12px 16px 12px 40px;
      font-size: 14px; font-weight: 500; transition: all 0.2s ease;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .seminar-control input:focus {
      border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px var(--primary-soft);
    }

    /* --- Grid & Cards --- */
    .seminar-grid { 
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(min(100%, 400px), 1fr)); 
      gap: 24px; 
    }
    .seminar-card {
      background: var(--card-bg); border: 1px solid var(--border-color);
      border-radius: var(--radius-lg); padding: 24px;
      display: flex; gap: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
      position: relative; cursor: pointer;
    }
    .seminar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
      border-color: var(--primary-light);
    }

    .seminar-date {
      width: 72px; height: 82px; min-width: 72px;
      background: var(--primary-soft); border-radius: var(--radius-md);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .seminar-day { font-size: 28px; font-weight: 800; color: var(--primary); line-height: 1; }
    .seminar-month { font-size: 10px; font-weight: 700; color: var(--primary-light); text-transform: uppercase; margin-top: 4px; }

    .seminar-content { flex: 1; min-width: 0; }
    .seminar-name {
      font-size: 18px; font-weight: 700; line-height: 1.4; margin-bottom: 12px; margin-top: 4px;
      color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .seminar-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .seminar-tag {
      background: #f8fafc; border: 1px solid #f1f5f9;
      padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600;
      color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px;
    }
    
    .seminar-chip {
      position: absolute; top: 15px; right: 15px;
      padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800;
      text-transform: uppercase; letter-spacing: 0.02em;
    }
    .chip-today { background: var(--today-bg); color: var(--today-text); }
    .chip-tom { background: var(--tom-bg); color: var(--tom-text); }
    .chip-up { background: var(--up-bg); color: var(--up-text); }

    .seminar-meta {
      margin-top: 16px; padding-top: 16px; border-top: 1px solid #f8fafc;
      display: flex; gap: 15px; font-size: 13px; font-weight: 500; color: var(--text-muted);
    }
    .seminar-meta i { color: var(--primary); opacity: 0.7; }

    /* --- Responsive Adjustments --- */
    @media (max-width: 576px) {
      .seminar-card { flex-direction: column; padding: 20px; }
      .seminar-date { width: 100%; height: auto; padding: 12px; flex-direction: row; gap: 10px; }
      .seminar-month { margin-top: 0; font-size: 14px; }
      .seminar-chip { position: static; display: inline-block; margin-bottom: 12px; width: fit-content; }
      .seminar-top { margin-bottom: 24px; }
      .seminar-title h4 { font-size: 22px; }
    }

    /* --- Modal Customization --- */
    .seminar-modal .modal-content { border: none; border-radius: var(--radius-xl); overflow: hidden; }
    .seminar-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 24px; }
    .modal-body { padding: 24px; background: #fcfcfd; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    
    @media (max-width: 768px) {
      .detail-grid { grid-template-columns: 1fr; }
      .seminar-filters { flex-direction: column; }
    }
  </style>

  <div class="seminar-wrap">
    <div class="seminar-top">
      <div class="seminar-title">
        <h4>Sắp diễn ra</h4>
      </div>

      <form action="{{ url()->current() }}" method="GET" class="seminar-filters">
        <div class="seminar-control">
          <span class="prefix"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên..." onchange="this.form.submit()">
        </div>

        <div class="seminar-control">
          <span class="prefix"><i class="fa-solid fa-layer-group"></i></span>
          <select name="category" onchange="this.form.submit()">
            <option value="">Tất cả loại</option>
            @foreach($categories as $c)
              <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>
                @php
                  $catMap = ['work' => 'Làm việc / Nghiên cứu', 'seminar' => 'Hội thảo / Seminar', 'other' => 'Khác'];
                  echo $catMap[$c] ?? ucfirst($c);
                @endphp
              </option>
            @endforeach
          </select>
        </div>
      </form>
    </div>

    <div class="seminar-grid">
      @forelse($upcomingEvents as $event)
        @php
          $badge = 'chip-up'; $badgeText = 'Sắp tới';
          if ($event->start->isToday()) { $badge = 'chip-today'; $badgeText = 'Hôm nay'; }
          else if ($event->start->isTomorrow()) { $badge = 'chip-tom'; $badgeText = 'Ngày mai'; }

          $labName = $event->lab->name ?? ($event->lab->code ?? ($event->lab_code ?? 'Chưa rõ'));
          $categoryMap = ['work' => 'Làm việc / Nghiên cứu', 'seminar' => 'Hội thảo / Seminar', 'other' => 'Khác'];
          $categoryText = $categoryMap[$event->category] ?? ucfirst($event->category ?? 'Chưa phân loại');
          $userName = $event->user?->full_name ?? $event->user?->name ?? 'Chưa rõ';
        @endphp

        <div class="seminar-card js-open-event" role="button" tabindex="0"
             data-bs-toggle="modal" data-bs-target="#eventDetailModal"
             data-title="{{ e($event->title) }}"
             data-category="{{ e($categoryText) }}"
             data-user="{{ e($userName) }}"
             data-date="{{ e($event->start->format('d/m/Y')) }}"
             data-day="{{ e($event->start->isoFormat('dddd')) }}"
             data-time="{{ e($event->start->format('H:i')) }}"
             data-status="{{ e($badgeText) }}"
             data-description="{{ e($event->description ?? 'Nội dung đang cập nhật.') }}"
             data-lab="{{ e($labName) }}">
          
          <div class="seminar-date">
            <div class="seminar-day">{{ $event->start->format('d') }}</div>
            <div class="seminar-month">Tháng {{ $event->start->format('m') }}</div>
          </div>

          <div class="seminar-content">
            <span class="seminar-chip {{ $badge }}">{{ $badgeText }}</span>
            <p class="seminar-name">{{ $event->title }}</p>

            <div class="seminar-tags">
              <span class="seminar-tag"><i class="fa-solid fa-tag"></i>{{ $categoryText }}</span>
              <span class="seminar-tag"><i class="fa-solid fa-door-open"></i>{{ $labName }}</span>
            </div>

            <div class="seminar-meta">
              <span><i class="fa-regular fa-clock"></i> {{ $event->start->format('H:i') }}</span>
              <span><i class="fa-regular fa-user"></i> {{ \Illuminate\Support\Str::limit($userName, 18) }}</span>
            </div>
          </div>
        </div>
      @empty
        <div class="w-100 text-center py-5 text-muted fw-medium">Hiện không có sự kiện sắp tới.</div>
      @endforelse
    </div>
  </div>
</x-client-layout>