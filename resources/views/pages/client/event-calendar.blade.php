<x-client-layout>
  <style>
    :root{
      --primary:#4f46e5; --primary-soft:#eef2ff;
      --text:#0f172a; --muted:#64748b;
      --card:#fff; --border:#e2e8f0;
      --radius:14px;
      --shadow:0 1px 2px rgb(0 0 0 / .04), 0 10px 24px rgb(15 23 42 / .06);
      --shadow-hover:0 1px 2px rgb(0 0 0 / .06), 0 18px 40px rgb(15 23 42 / .12);

      --today-bg:#fee2e2; --today-text:#991b1b;
      --tom-bg:#ffedd5; --tom-text:#9a3412;
      --up-bg:#dcfce7; --up-text:#166534;
    }

    .seminar-wrap{ width:100%; padding:28px 30px; font-family: ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial; color:var(--text);}
    .seminar-top{ display:flex; align-items:flex-end; justify-content:space-between; gap:14px; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);}
    .seminar-title{ display:flex; align-items:center; gap:10px;}
    .seminar-title .icon{ width:38px; height:38px; border-radius:12px; display:grid; place-items:center; background:var(--primary-soft); color:var(--primary);}
    .seminar-title h4{ margin:0; font-weight:1200; letter-spacing:-.02em; font-size:28px; line-height:1.6;}
    .seminar-sub{ margin:4px 0 0; color:var(--muted); font-size:13px; font-weight:600;}

    .seminar-filters{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:flex-end;}
    .seminar-control{ position:relative; min-width:220px;}
    .seminar-control input,.seminar-control select{
      width:100%; border:1px solid var(--border); background:#fff; color:var(--text);
      border-radius:12px; padding:10px 12px 10px 38px; font-size:14px; font-weight:600; outline:none; transition:.18s;
    }
    .seminar-control .prefix{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:14px; pointer-events:none;}
    .seminar-control input:focus,.seminar-control select:focus{ border-color:rgba(79,70,229,.45); box-shadow:0 0 0 4px rgba(79,70,229,.10);}

    .seminar-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(360px, 1fr)); gap:18px;}
    .seminar-card{
      background:var(--card); border:1px solid var(--border); border-radius:var(--radius);
      padding:18px; display:flex; gap:16px; min-height:150px;
      box-shadow:var(--shadow); transition:.18s; cursor:pointer; overflow:hidden; position:relative;
    }
    .seminar-card:hover{ transform:translateY(-3px); box-shadow:var(--shadow-hover); border-color:rgba(79,70,229,.25);}

    .seminar-date{
      width:78px; min-width:78px; height:78px; border-radius:16px; background:#f1f5f9;
      display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;
    }
    .seminar-day{ font-size:26px; font-weight:900; line-height:1; color:var(--primary); letter-spacing:-.02em;}
    .seminar-month{ margin-top:4px; font-size:11px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.06em;}

    .seminar-content{ flex:1; display:flex; flex-direction:column; min-width:0;}
    .seminar-head{ display:flex; justify-content:space-between; gap:10px; margin-bottom:8px;}
    .seminar-name{
      margin:0; font-size:16px; font-weight:900; line-height:1.35; letter-spacing:-.015em;
      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    }

    .seminar-chip{ padding:5px 10px; border-radius:999px; font-size:11px; font-weight:900; text-transform:uppercase; white-space:nowrap;}
    .chip-today{  color:var(--today-text);}
    .chip-tom{ color:var(--tom-text);}
    .chip-up{ color:var(--up-text);}

    .seminar-tags{ display:flex; flex-wrap:wrap; gap:8px; margin-top:6px;}
    .seminar-tag{
      display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:900;
      text-transform:uppercase; color:var(--muted); border:1px solid var(--border);
      background:#fff; border-radius:999px; padding:4px 10px; width:fit-content;
    }

    .seminar-desc{
      margin:10px 0 0; color:#475569; font-size:13.5px; line-height:1.55;
      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    }

    .seminar-meta{ margin-top:auto; padding-top:12px; display:flex; flex-wrap:wrap; gap:12px; color:var(--muted); font-size:12.5px; font-weight:700;}
    .seminar-meta .item{ display:inline-flex; align-items:center; gap:7px;}
    .seminar-meta .item i{ color: rgba(79,70,229,.9);}

    .seminar-empty{ border:1px dashed var(--border); background:#fff; border-radius:16px; padding:26px; text-align:center; color:var(--muted); font-weight:700;}

    /* Modal */
    .seminar-modal .modal-content{ border:0; border-radius:18px; overflow:hidden; box-shadow: 0 20px 60px rgb(15 23 42 / .18);}
    .seminar-modal .modal-header{ background: linear-gradient(135deg, rgba(79,70,229,.10), rgba(79,70,229,.02)); border-bottom:1px solid rgba(226,232,240,.9);}
    .seminar-modal .modal-title{ font-weight:900; letter-spacing:-.02em;}
    .seminar-modal .badge-soft{ display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:900; background:#fff; border:1px solid var(--border); color:var(--text);}
    .seminar-modal .detail-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:14px;}
    .seminar-modal .detail-item{ border:1px solid var(--border); border-radius:14px; padding:12px; background:#fff;}
    .seminar-modal .detail-label{ color:var(--muted); font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;}
    .seminar-modal .detail-value{ font-size:14px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:8px; word-break:break-word;}
    .seminar-modal .detail-value i{ color:rgba(79,70,229,.9);}
    .seminar-modal .desc-box{ margin-top:14px; border:1px solid var(--border); background:#fff; border-radius:14px; padding:14px;}
    .seminar-modal .desc-box p{ margin:0; color:#334155; font-size:14px; line-height:1.65; font-weight:600; white-space:pre-wrap;}

    .seminar-modal .files-box{ margin-top:12px; border:1px solid var(--border); background:#fff; border-radius:14px; padding:14px;}
    .seminar-modal .files-list{ margin:0; padding:0; list-style:none; display:flex; flex-direction:column; gap:10px;}
    .seminar-modal .file-item{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      border:1px solid var(--border); border-radius:12px; padding:10px 12px;
    }
    .seminar-modal .file-left{ display:flex; align-items:center; gap:10px; min-width:0;}
    .seminar-modal .file-left i{ color:rgba(79,70,229,.9); font-size:16px;}
    .seminar-modal .file-name{ font-weight:800; color:var(--text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:420px;}
    .seminar-modal .file-actions a{
      text-decoration:none; font-weight:900; font-size:12px; text-transform:uppercase; letter-spacing:.04em;
      color:var(--primary-600);
    }
    .seminar-modal .file-actions a:hover{ text-decoration:underline; }

    @media (max-width: 640px){
      .seminar-wrap{ padding:16px; }
      .seminar-top{ align-items:flex-start; flex-direction:column; }
      .seminar-control{ min-width: 100%; }
      .seminar-grid{ grid-template-columns:1fr; }
      .seminar-modal .detail-grid{ grid-template-columns:1fr; }
      .seminar-modal .file-name{ max-width:220px; }
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
                {{ ucfirst($c) }}
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

          $filesPayload = $event->files?->map(function($f){
            return [
              'name' => $f->name ?? basename($f->path ?? ''),
              'url'  => $f->url ?? (isset($f->path) ? asset($f->path) : null),
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
          data-category="{{ e($event->category ?? 'General') }}"
          data-user="{{ e($event->user?->name ?? 'Unknown') }}"
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
                  <span class="seminar-tag"><i class="fa-solid fa-tag"></i>{{ ucfirst($event->category ?? 'General') }}</span>
                  <span class="seminar-tag"><i class="fa-solid fa-door-open"></i>{{ $labName }}</span>
                </div>
              </div>
              <span class="seminar-chip {{ $badge }}">{{ $badgeText }}</span>
            </div>

            <p class="seminar-desc">
              {{ $event->description ? \Illuminate\Support\Str::limit($event->description, 110) : 'Nội dung đang cập nhật.' }}
            </p>

            <div class="seminar-meta">
              <span class="item"><i class="fa-regular fa-clock"></i> {{ $event->start->format('H:i') }}</span>
              <span class="item"><i class="fa-regular fa-user"></i> {{ $event->user ? \Illuminate\Support\Str::limit($event->user->name, 18) : 'Unknown' }}</span>
              <span class="item"><i class="fa-solid fa-door-open"></i> {{ $labName }}</span>
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

        {{-- <div class="modal-footer">
          <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Đóng</button>
        </div> --}}
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
