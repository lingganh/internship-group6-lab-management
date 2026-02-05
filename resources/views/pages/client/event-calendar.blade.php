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
        max-width: 1400px;
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
        font-size: 26px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.03em;
        color: #1e293b;
      }

      .seminar-filters {
        display: flex;
        gap: 10px;
        flex-grow: 1;
        justify-content: flex-end;
      }

      .seminar-control input,
      .seminar-control select {
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 14px;
        background: var(--bg-sub);
        outline: none;
        transition: all 0.2s;
        min-width: 200px;
      }

      .seminar-control input:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.05);
      }

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

      .col-stt {
        width: 60px;
      }

      .col-time {
        width: 200px;
      }

      .col-event {
        width: auto;
        min-width: 180px;
      }

      .col-cat {
        width: 180px;
      }

      .col-user {
        width: 200px;
      }

      .col-desc {
        width: 250px;
      }

      .col-files {
        width: 120px;
      }

      .col-status {
        width: 120px;
      }

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

      .custom-table tbody tr:hover {
        background-color: #f8fafc;
        cursor: pointer;
      }

      /* --- DateTime & Badges --- */
      .datetime-block {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      .date-icon-box {
        background: #eff6ff;
        color: var(--primary);
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        line-height: 1.1;
        flex-shrink: 0;
      }

      .date-icon-box span {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.8;
      }

      .time-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
      }

      .time-val {
        font-weight: 600;
        color: var(--text-main);
        font-size: 14px;
        white-space: nowrap;
      }

      .badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 12px;
        display: inline-block;
        white-space: nowrap;
      }

      .chip-today {
        color: #e11d48;
        background: #fff1f2;
      }

      .chip-tom {
        color: #d97706;
        background: #fff7ed;
      }

      .chip-up {
        color: #16a34a;
        background: #f0fdf4;
      }

      /* --- User Info với Group --- */
      .user-info-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
      }

      .user-name {
        font-weight: 600;
        color: var(--text-main);
      }

      .user-group {
        font-size: 12px;
        color: var(--text-muted);
        font-style: italic;
      }

      /* --- Description --- */
      .desc-text {
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        line-height: 1.4;
        color: var(--text-muted);
        font-size: 13px;
      }

      /* --- File Download Button --- */
      .file-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: #eff6ff;
        color: var(--primary);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #dbeafe;
      }

      .file-download-btn:hover {
        background: var(--primary);
        color: #fff;
        transform: translateY(-1px);
      }

      .no-files {
        color: var(--text-muted);
        font-size: 12px;
        font-style: italic;
      }

      /* --- Responsive Mobile --- */
      @media (max-width: 768px) {
        .seminar-wrap {
          padding: 20px 10px;
        }

        .seminar-title h3 {
          font-size: 20px;
          text-align: center;
          width: 100%;
        }

        .custom-table, .custom-table tbody, .custom-table tr, .custom-table td {
          display: block;
          width: 100%;
        }

        .custom-table thead {
          display: none;
        }

        .custom-table tr {
          margin-bottom: 1rem;
          border: 1px solid var(--border-color);
          border-radius: var(--radius);
          background: #fff;
          padding: 10px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .custom-table td {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          text-align: right;
          padding: 8px 5px;
          border-bottom: 1px dashed #eee;
          min-height: 45px;
        }

        .custom-table td:last-child {
          border-bottom: none;
        }

        .custom-table td::before {
          content: attr(data-label);
          font-weight: 700;
          color: var(--text-muted);
          font-size: 12px;
          text-transform: uppercase;
          text-align: left;
          flex: 1;
        }

        .custom-table td > div, 
        .custom-table td > span {
          flex: 2;
          display: flex;
          justify-content: flex-end;
          flex-direction: column;
          align-items: flex-end;
        }

        .date-icon-box {
          width: 38px;
          height: 38px;
          font-size: 14px;
        }
        
        .date-icon-box span {
          font-size: 8px;
        }

        .time-val {
          font-size: 13px;
        }

        .stt-col {
          display: none !important;
        }

        .pagination-wrap {
          width: 100%;
          overflow-x: auto;
          display: flex;
          justify-content: center;
        }
        
        .pagination {
          flex-wrap: wrap;
          justify-content: center;
          gap: 5px;
        }
      }

      /* Tablet */
      @media (min-width: 769px) and (max-width: 1024px) {
        .col-time { width: 180px; }
        .col-cat { width: 150px; }
        .col-user { width: 180px; }
        .col-desc { width: 200px; }
        .custom-table td { padding: 12px 10px; font-size: 13px; }
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
      </div>
    </div>

    <div class="table-responsive shadow-sm">
      <table class="custom-table" wire:loading.class="opacity-50">
        <thead>
          <tr>
            <th class="col-stt">STT</th>
            <th class="col-time">Thời gian</th>
            <th class="col-event">Sự kiện</th>
            <th class="col-cat">Loại</th>
            <th class="col-user">Phụ trách</th>
            <th class="col-desc">Mô tả</th>
            <th class="col-files">Tệp đính kèm</th>
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
              
              // Lấy tên nhóm từ registered_for
              $groupName = null;
              if ($event->registered_for) {
                $group = \App\Models\Group::find($event->registered_for);
                $groupName = $group ? $group->name : null;
              }
              
              // Đếm số file đính kèm
              $filesCount = $event->files()->count();
            @endphp
            <tr wire:key="event-{{ $event->id }}">
              <td class="stt-col" data-label="STT">{{ $index + 1 }}</td>
              
              <td data-label="Thời gian">
                <div class="datetime-block">
                  <div class="date-icon-box">
                    {{ $event->start->format('d') }}
                    <span>T{{ $event->start->format('m') }}</span>
                  </div>
                  <div class="time-info">
                    <div class="time-val">
                      {{ $event->start->format('H:i') }}
                      @if($event->end)— {{ $event->end->format('H:i') }}@endif
                    </div>
                    <div class="day-val" style="font-size: 12px; color: var(--text-muted);">
                      {{ $event->start->isoFormat('dddd') }}
                    </div>
                  </div>
                </div>
              </td>
              
              <td data-label="Sự kiện">
                <span class="event-title-text" style="font-weight: 600;">{{ $event->title }}</span>
              </td>
              
              <td data-label="Loại">
                <span class="event-cat-text">{{ $categoryMap[$event->category] ?? 'Khác' }}</span>
              </td>
              
              <td data-label="Phụ trách">
                <div class="user-info-block">
                  <span class="user-name">{{ $event->user->full_name ?? $event->user->name ?? 'N/A' }}</span>
                  @if($groupName)
                    <span class="user-group">Đăng ký cho: {{ $groupName }}</span>
                  @endif
                </div>
              </td>
              
              <td data-label="Mô tả">
                @if($event->description)
                  <div class="desc-text">{{ $event->description }}</div>
                @else
                  <span class="no-files">Không có mô tả</span>
                @endif
              </td>
              
              <td data-label="Tệp đính kèm">
                @if($filesCount > 0)
                  <a href="#" class="file-download-btn" 
                     onclick="event.stopPropagation(); showFilesModal({{ $event->id }}); return false;">
                    <i class="fa-solid fa-paperclip"></i>
                    <span>{{ $filesCount }} file</span>
                  </a>
                @else
                  <span class="no-files">Không có file</span>
                @endif
              </td>
              
              <td align="center" data-label="Trạng thái">
                <span class="badge-status {{ $badgeClass }}">{{ $badgeText }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                Hiện tại chưa có sự kiện nào.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <br>
    <div class="pagination-wrap">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="pagination-controls">
          {{ $upcomingEvents->links('vendor.pagination.bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>

  {{-- Modal hiển thị danh sách files --}}
  <div class="modal fade" id="filesModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tệp đính kèm</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="filesModalBody">
          <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Đang tải...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    async function showFilesModal(eventId) {
      const modal = new bootstrap.Modal(document.getElementById('filesModal'));
      const modalBody = document.getElementById('filesModalBody');
      
      modal.show();
      
      try {
        const response = await fetch(`/api/lab-events/${eventId}/files`);
        const data = await response.json();
        
        if (data.files && data.files.length > 0) {
          let html = '<div class="list-group">';
          data.files.forEach(file => {
            html += `
              <a href="/storage/${file.file_path}" 
                 class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                 download="${file.file_name}" 
                 target="_blank">
                <div>
                  <i class="fa-solid fa-file me-2"></i>
                  <span>${file.file_name}</span>
                </div>
                <span class="badge bg-primary rounded-pill">${formatFileSize(file.file_size)}</span>
              </a>
            `;
          });
          html += '</div>';
          modalBody.innerHTML = html;
        } else {
          modalBody.innerHTML = '<p class="text-center text-muted">Không có file đính kèm</p>';
        }
      } catch (error) {
        modalBody.innerHTML = '<p class="text-center text-danger">Có lỗi xảy ra khi tải danh sách file</p>';
      }
    }
    
    function formatFileSize(bytes) {
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
  </script>
</x-client-layout>