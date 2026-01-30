<x-client-layout>
  <style>
  :root {
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-soft: #f5f3ff;
    --bg-main: #f8fafc;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --card-bg: #ffffff;
    --border-color: #f1f5f9;
    --radius-lg: 16px;
    --radius-md: 12px;
    --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    
    /* Status Colors */
    --today-bg: #fef2f2; --today-text: #ef4444;
    --tom-bg: #fff7ed; --tom-text: #f59e0b;
    --up-bg: #f0fdf4; --up-text: #22c55e;
  }

  .seminar-wrap { 
    width: 100%; padding: 32px; 
    background-color: var(--bg-main);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--text-main);
  }

  /* Header & Filters */
  .seminar-top { 
    display: flex; align-items: center; justify-content: space-between; 
    margin-bottom: 32px; gap: 20px;
  }
  .seminar-title h4 { 
    font-size: 24px; font-weight: 800; margin: 0; color: #0f172a;
    letter-spacing: -0.025em;
  }
  
  .seminar-filters { display: flex; gap: 12px; }
  .seminar-control { position: relative; }
  .seminar-control input, .seminar-control select {
    border: 1px solid #e2e8f0; background: #fff;
    border-radius: var(--radius-md); padding: 10px 16px 10px 40px;
    font-size: 14px; font-weight: 500; transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
  }
  .seminar-control input:focus {
    border-color: var(--primary); ring: 2px var(--primary-soft);
    outline: none; box-shadow: 0 0 0 3px var(--primary-soft);
  }

  /* Grid & Cards */
  .seminar-grid { 
    display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); 
    gap: 24px; 
  }
  .seminar-card {
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: var(--radius-lg); padding: 20px;
    display: flex; gap: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-sm); position: relative;
  }
  .seminar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -8px rgba(0,0,0,0.1);
    border-color: var(--primary-light);
  }

  /* Date Badge */
  .seminar-date {
    width: 70px; height: 85px; min-width: 70px;
    background: var(--primary-soft); border-radius: var(--radius-md);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
  }
  .seminar-day { font-size: 28px; font-weight: 800; color: var(--primary); line-height: 1; }
  .seminar-month { font-size: 11px; font-weight: 700; color: var(--primary-light); text-transform: uppercase; margin-top: 4px; }

  /* Content */
  .seminar-name {
    font-size: 17px; font-weight: 700; line-height: 1.4; margin-bottom: 8px;
    color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  }
  .seminar-tag {
    background: #f8fafc; border: 1px solid #edf2f7;
    padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600;
    color: var(--text-muted); display: inline-flex; align-items: center; gap: 5px;
  }
  
  .seminar-chip {
    position: absolute; top: 15px; right: 15px;
    padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
  }
  .chip-today { background: var(--today-bg); color: var(--today-text); }
  .chip-tom { background: var(--tom-bg); color: var(--tom-text); }
  .chip-up { background: var(--up-bg); color: var(--up-text); }

  .seminar-meta {
    margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9;
    display: flex; gap: 15px; font-size: 13px; font-weight: 500; color: var(--text-muted);
  }
  .seminar-meta i { color: var(--primary); opacity: 0.8; }

  /* Modal Styling */
  .seminar-modal .modal-content { border: none; border-radius: 20px; }
  .seminar-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 24px; }
  .detail-item {
    background: #f8fafc; border: 1px solid #f1f5f9;
    border-radius: 12px; padding: 16px; transition: all 0.2s;
  }
  .detail-item:hover { background: #fff; border-color: var(--primary-light); }
  .detail-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px; font-weight: 700; }
  .detail-value { font-weight: 700; color: var(--text-main); font-size: 15px; }

  @media (max-width: 768px) {
    .seminar-top { flex-direction: column; align-items: flex-start; }
    .seminar-filters { width: 100%; }
    .seminar-control { flex: 1; }
    .seminar-grid { grid-template-columns: 1fr; }
  }
</style>
  <div class="seminar-wrap">
    <div class="seminar-top">
      <div>
        <div class="seminar-title">
          <div class="icon"><i class="fa-regular fa-calendar-check"></i></div>
          <div>
            <h4><b>Sắp diễn ra</b></h4>
          </div>
        </div>
      </div>

      <form action="{{ url()->current() }}" method="GET" class="seminar-filters">
        <div class="seminar-control">
          <span class="prefix"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên..." onchange="this.form.submit()">
        </div>

        <div class="seminar-control" style="min-width: 200px;">
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
          $labCode = $event->lab->code ?? ($event->lab_code ?? null);

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

        <div
          class="seminar-card js-open-event"
          role="button"
          tabindex="0"
          data-bs-toggle="modal"
          data-bs-target="#eventDetailModal"
          data-id="{{ $event->id }}"
          data-title="{{ e($event->title) }}"
          data-category="{{ e($categoryText) }}"
          data-user="{{ e($userName) }}"
          data-date="{{ e($event->start->format('d/m/Y')) }}"
          data-day="{{ e($event->start->isoFormat('dddd')) }}"
          data-time="{{ e($event->start->format('H:i')) }}"
          data-status="{{ e($badgeText) }}"
          data-description="{{ e($event->description ?? 'Nội dung đang cập nhật.') }}"
          data-lab="{{ e($labName) }}"
          data-labcode="{{ e($labCode ?? '') }}"
        >
          <div class="seminar-date">
            <div class="seminar-day">{{ $event->start->format('d') }}</div>
            <div class="seminar-month">Tháng {{ $event->start->format('m') }}</div>
          </div>

          <div class="seminar-content">
            <div class="seminar-head">
              <div style="min-width:0;">
                <p class="seminar-name">{{ $event->title }}</p>

                <div class="seminar-tags">
                  <span class="seminar-tag"><i class="fa-solid fa-tag"></i>{{ $categoryText }}</span>
                  <span class="seminar-tag"><i class="fa-solid fa-door-open"></i>{{ $labName }}</span>
                </div>
              </div>
              <span class="seminar-chip {{ $badge }}">{{ $badgeText }}</span>
            </div>

            {{-- <p class="seminar-desc">
              {{ $event->description ? \Illuminate\Support\Str::limit($event->description, 110) : 'Nội dung đang cập nhật.' }}
            </p> --}}

            <div class="seminar-meta">
              <span class="item"><i class="fa-regular fa-clock"></i> {{ $event->start->format('H:i') }}</span>
              <span class="item"><i class="fa-regular fa-user"></i> {{ \Illuminate\Support\Str::limit($userName, 18) }}</span>
              {{-- <span class="item"><i class="fa-solid fa-door-open"></i> {{ $labName }}</span> --}}
            </div>
          </div>

          {{-- payload files --}}
          <script type="application/json" class="evt-files">
            {!! $filesPayload->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
          </script>
        </div>
      @empty
        <div class="seminar-empty">Hiện không có sự kiện sắp tới.</div>
      @endforelse
    </div>
  </div>

  {{-- MODAL --}}
  <div class="modal fade seminar-modal" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-1" id="evtTitle">Chi tiết sự kiện</h5>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge-soft" id="evtStatus"><i class="fa-regular fa-circle-check"></i><span>—</span></span>
              <span class="badge-soft" id="evtCategory"><i class="fa-solid fa-tag"></i><span>—</span></span>
              <span class="badge-soft" id="evtLab"><i class="fa-solid fa-door-open"></i><span>—</span></span>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="detail-grid">
            <div class="detail-item">
              <div class="detail-label">Thời gian</div>
              <div class="detail-value"><i class="fa-regular fa-clock"></i><span id="evtTime">—</span></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Ngày</div>
              <div class="detail-value"><i class="fa-solid fa-calendar-day"></i><span id="evtDate">—</span></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Thứ</div>
              <div class="detail-value"><i class="fa-regular fa-calendar"></i><span id="evtDay">—</span></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Người tạo</div>
              <div class="detail-value"><i class="fa-regular fa-user"></i><span id="evtUser">—</span></div>
            </div>
          </div>

          <div class="desc-box">
            <div class="detail-label mb-2">Mô tả</div>
            <p id="evtDesc">—</p>
          </div>

          <div class="files-box">
            <div class="detail-label mb-2">File đính kèm</div>
            <ul class="files-list" id="evtFiles">
              <li class="text-muted fw-semibold">Không có file đính kèm.</li>
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
          filesEl.innerHTML = '<li class="text-muted fw-semibold">Không có file đính kèm.</li>';
          return;
        }
        files.forEach(f => {
          const name = f?.name || 'Tệp đính kèm';
          const url = f?.url || '#';
          const li = document.createElement('li');
          li.className = 'file-item';
          li.innerHTML = `
            <div class="file-left">
              <i class="fa-regular fa-file-lines"></i>
              <div class="file-name" title="${name.replaceAll('"','&quot;')}">${name}</div>
            </div>
            <div class="file-actions">
              ${url !== '#' ? `<a href="${url}" target="_blank" rel="noopener">Mở / tải</a>` : `<span class="text-muted">Không có link</span>`}
            </div>
          `;
          filesEl.appendChild(li);
        });
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

          // files from JSON script
          let files = [];
          const jsonNode = card.querySelector('.evt-files');
          if(jsonNode){
            try { files = JSON.parse(jsonNode.textContent || '[]'); } catch(e){ files = []; }
          }
          renderFiles(files);
        };

        card.addEventListener('click', open);
        card.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            open();
            card.click();
          }
        });
      });

      const modal = document.getElementById('eventDetailModal');
      if(modal){
        modal.addEventListener('hidden.bs.modal', () => {
          setText(titleEl, 'Chi tiết sự kiện');
          setText(statusEl, '—');
          setText(categoryEl, '—');
          setText(labEl, '—');
          setText(timeEl, '—');
          setText(dateEl, '—');
          setText(dayEl, '—');
          setText(userEl, '—');
          setText(descEl, '—');
          filesEl.innerHTML = '<li class="text-muted fw-semibold">Không có file đính kèm.</li>';
        });
      }
    })();
  </script>
</x-client-layout>