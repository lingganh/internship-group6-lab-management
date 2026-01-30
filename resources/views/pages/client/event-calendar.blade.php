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
      width: 100%; padding: 40px 24px; 
      background-color: var(--bg-main);
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: var(--text-main);
    }

    /* --- Header Section --- */
    .seminar-top { 
      display: flex; align-items: center; justify-content: space-between; 
      margin-bottom: 40px; gap: 20px;
    }
    .seminar-title h4 { 
      font-size: 28px; font-weight: 800; margin: 0; color: #0f172a;
      letter-spacing: -0.03em;
    }
    
    .seminar-filters { display: flex; gap: 12px; }
    .seminar-control { position: relative; }
    .seminar-control .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); font-size: 14px; pointer-events: none;
    }
    .seminar-control input, .seminar-control select {
      border: 1px solid #e2e8f0; background: #fff;
      border-radius: var(--radius-md); padding: 12px 16px 12px 40px;
      font-size: 14px; font-weight: 500; transition: all 0.2s ease;
      box-shadow: 0 1px 2px rgba(0,0,0,0.05); min-width: 220px;
    }
    .seminar-control input:focus {
      border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px var(--primary-soft);
    }

    /* --- Grid & Cards --- */
    .seminar-grid { 
      display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); 
      gap: 24px; 
    }
    .seminar-card {
      background: var(--card-bg); border: 1px solid var(--border-color);
      border-radius: var(--radius-lg); padding: 24px;
      display: flex; gap: 20px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
      position: relative; cursor: pointer;
    }
    .seminar-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);
      border-color: var(--primary-light);
    }

    .seminar-date {
      width: 72px; height: 82px; min-width: 72px;
      background: var(--primary-soft); border-radius: var(--radius-md);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .seminar-day { font-size: 28px; font-weight: 800; color: var(--primary); line-height: 1; }
    .seminar-month { font-size: 10px; font-weight: 700; color: var(--primary-light); text-transform: uppercase; margin-top: 4px; letter-spacing: 0.05em; }

    .seminar-content { flex: 1; min-width: 0; }
    .seminar-name {
      font-size: 18px; font-weight: 700; line-height: 1.4; margin-bottom: 12px;
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

    /* --- Modal Sophistication --- */
    .seminar-modal .modal-content { border: none; border-radius: var(--radius-xl); overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
    .seminar-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 32px 32px 24px; background: #fff; }
    .modal-title { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
    
    .badge-soft {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 10px;
        font-size: 12px; font-weight: 700; background: var(--primary-soft); color: var(--primary);
    }
    
    .modal-body { padding: 32px; background: #fcfcfd; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 28px; }
    .detail-item { background: #fff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 18px; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .detail-item:hover { border-color: var(--primary-light); transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
    .detail-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 8px; font-weight: 800; }
    .detail-value { font-weight: 700; color: var(--text-main); font-size: 15px; display: flex; align-items: center; gap: 10px; }
    .detail-value i { color: var(--primary); font-size: 16px; }

    .info-section-title { font-size: 12px; font-weight: 800; color: #94a3b8; margin: 24px 0 12px 4px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em;}
    .desc-box { background: #fff; border: 1px solid #f1f5f9; border-radius: 18px; padding: 20px; color: #475569; line-height: 1.7; font-size: 15px; }
    
    .files-list { padding: 0; margin: 0; list-style: none; }
    .file-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; margin-bottom: 10px;
    }
    .file-name { font-weight: 600; font-size: 14px; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
    .file-name i { color: #94a3b8; }
    .file-actions a {
        color: var(--primary); font-weight: 700; font-size: 13px; text-decoration: none;
        padding: 6px 14px; border-radius: 8px; background: var(--primary-soft); transition: all 0.2s;
    }
    .file-actions a:hover { background: var(--primary); color: #fff; }

    @media (max-width: 768px) {
      .seminar-top { flex-direction: column; align-items: flex-start; }
      .seminar-filters { width: 100%; }
      .seminar-control { flex: 1; }
      .seminar-control input, .seminar-control select { min-width: 100%; }
      .seminar-grid { grid-template-columns: 1fr; }
      .detail-grid { grid-template-columns: 1fr; }
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

          $filesPayload = $event->files?->map(function($f){
            return [
              'name' => $f->file_name ?? basename($f->file_path ?? ''),
              'url'  => $f->file_path ? asset('storage/' . $f->file_path) : null,
            ];
          })->values() ?? collect([]);
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

          <script type="application/json" class="evt-files">
            {!! $filesPayload->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
          </script>
        </div>
      @empty
        <div class="w-100 text-center py-5 text-muted fw-medium">Hiện không có sự kiện sắp tới.</div>
      @endforelse
    </div>
  </div>

  <div class="modal fade seminar-modal" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between align-items-start">
          <div>
            <h5 class="modal-title mb-3" id="evtTitle">Chi tiết sự kiện</h5>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge-soft" id="evtStatus"><i class="fa-regular fa-circle-check"></i><span>—</span></span>
              <span class="badge-soft" id="evtCategory"><i class="fa-solid fa-tag"></i><span>—</span></span>
              <span class="badge-soft" id="evtLab"><i class="fa-solid fa-door-open"></i><span>—</span></span>
            </div>
          </div>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="detail-grid">
            <div class="detail-item">
              <div class="detail-label">Thời gian</div>
              <div class="detail-value"><i class="fa-regular fa-clock"></i><span id="evtTime">—</span></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Ngày tổ chức</div>
              <div class="detail-value"><i class="fa-regular fa-calendar-check"></i><span id="evtDate">—</span></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Thứ</div>
              <div class="detail-value"><i class="fa-solid fa-calendar-day"></i><span id="evtDay">—</span></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Người phụ trách</div>
              <div class="detail-value"><i class="fa-regular fa-circle-user"></i><span id="evtUser">—</span></div>
            </div>
          </div>

          <div class="info-section-title"><i class="fa-solid fa-align-left"></i> Nội dung chi tiết</div>
          <div class="desc-box">
            <p id="evtDesc" class="m-0">—</p>
          </div>

          <div class="info-section-title"><i class="fa-solid fa-paperclip"></i> Tài liệu đính kèm</div>
          <div id="evtFiles">
            <ul class="files-list">
               <li class="text-muted small p-3 text-center border rounded-3 bg-light">Không có file đính kèm.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const titleEl = document.getElementById('evtTitle');
      const statusEl = document.querySelector('#evtStatus span');
      const categoryEl = document.querySelector('#evtCategory span');
      const labEl = document.querySelector('#evtLab span');
      const timeEl = document.getElementById('evtTime');
      const dateEl = document.getElementById('evtDate');
      const dayEl = document.getElementById('evtDay');
      const userEl = document.getElementById('evtUser');
      const descEl = document.getElementById('evtDesc');
      const filesEl = document.getElementById('evtFiles');

      function setText(el, v){ if(el) el.textContent = (v && String(v).trim() !== '') ? v : '—'; }

      function renderFiles(files){
        filesEl.innerHTML = '';
        if(!Array.isArray(files) || files.length === 0){
          filesEl.innerHTML = '<div class="text-muted small p-3 text-center border rounded-3 bg-light">Không có file đính kèm.</div>';
          return;
        }
        const ul = document.createElement('ul');
        ul.className = 'files-list';
        files.forEach(f => {
          const name = f?.name || 'Tệp đính kèm';
          const url = f?.url || '#';
          const li = document.createElement('li');
          li.className = 'file-item';
          li.innerHTML = `
            <div class="file-name"><i class="fa-regular fa-file-lines"></i><span>${name}</span></div>
            <div class="file-actions">
              ${url !== '#' ? `<a href="${url}" target="_blank">Mở / tải</a>` : `<span class="text-muted small">N/A</span>`}
            </div>
          `;
          ul.appendChild(li);
        });
        filesEl.appendChild(ul);
      }

      document.querySelectorAll('.js-open-event').forEach(card => {
        const open = () => {
          const ds = card.dataset;
          setText(titleEl, ds.title);
          setText(statusEl, ds.status);
          setText(categoryEl, ds.category);
          setText(labEl, ds.lab);
          setText(timeEl, ds.time);
          setText(dateEl, ds.date);
          setText(dayEl, ds.day);
          setText(userEl, ds.user);
          setText(descEl, ds.description);

          let files = [];
          const jsonNode = card.querySelector('.evt-files');
          if(jsonNode){
            try { files = JSON.parse(jsonNode.textContent || '[]'); } catch(e){ files = []; }
          }
          renderFiles(files);
        };
        card.addEventListener('click', open);
      });
    })();
  </script>
</x-client-layout>