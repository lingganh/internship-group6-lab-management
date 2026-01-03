<div>
  {{-- SESSION TOAST PAYLOAD (đọc từ session flash) --}}
  <div
    id="toastPayload"
    data-success="{{ session('success') }}"
    data-error="{{ session('error') }}"
    data-warning="{{ session('warning') }}"
    data-info="{{ session('info') }}"
    style="display:none"
  ></div>

  <div class="container-fluid py-4 diary-page">
    <div class="row justify-content-center">
      <div class="col-12 col-xxl-11">
        <div class="card border-0 diary-card">
          <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-row align-items-center justify-content-between gap-3">
              <div>
                <h4 class="mb-0 fw-bold text-dark">Nhật ký sử dụng</h4>
              </div>
            </div>
          </div>

          <div class="card-body pt-3">
            <div class="diary-filters mb-3">
              <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                  <select wire:model.live="filterLabCode" class="form-select diary-control">
                    <option value="">Tất cả</option>
                    @foreach($labs as $lab)
                      <option wire:key="lab-{{ $lab->code }}" value="{{ $lab->code }}">{{ $lab->name }} ({{ $lab->code }})</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-12 col-md-2">
                  <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                  <select wire:model.live="filterStatus" class="form-select diary-control">
                    <option value="">Tất cả</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="cancelled">Đã từ chối</option>
                  </select>
                </div>

                <div class="col-12 col-md-2">
                  <label class="form-label small fw-semibold text-dark mb-1">Từ ngày</label>
                  <input type="date" wire:model.live="filterFrom" class="form-control diary-control">
                </div>

                <div class="col-12 col-md-2">
                  <label class="form-label small fw-semibold text-dark mb-1">Đến ngày</label>
                  <input type="date" wire:model.live="filterTo" class="form-control diary-control">
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Từ khóa</label>
                  <input type="text" wire:model.live="keyword" class="form-control diary-control" placeholder="Tiêu đề / mô tả / feedback...">
                </div>
              </div>
            </div>

            <div class="table-responsive diary-table-wrap">
              <table class="table align-middle mb-0 diary-table">
                <thead>
                  <tr>
                    <th style="min-width: 280px;">Nội dung</th>
                    <th style="min-width: 160px;">Phòng</th>
                    <th style="min-width: 180px;">Người đăng ký</th>
                    <th style="min-width: 210px;">Thời gian</th>
                    <th class="text-center" style="width: 150px;">Trạng thái</th>
                    <th class="text-end" style="width: 170px;">Hành động</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse($events as $item)
                    <tr wire:key="event-{{ $item->id }}">
                      <td>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 520px;">{{ $item->title }}</div>
                        <div class="small text-muted">#{{ $item->id }} • {{ $this->categoryLabel($item->category) }}</div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark">{{ $item->lab?->name ?? ($item->lab_code ?? 'N/A') }}</div>
                        <div class="small text-muted">{{ $item->lab_code ?? '-' }}</div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark">{{ $item->user?->full_name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $item->user?->email ?? '' }}</div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark">{{ $item->start->format('d/m/Y') }}</div>
                        <div class="small text-muted">{{ $item->start->format('H:i') }} – {{ $item->end->format('H:i') }}</div>
                      </td>

                      <td class="text-center">
                        @if($item->status === 'pending')
                          <span class="badge diary-pill diary-pill-pending">Chờ duyệt</span>
                        @elseif($item->status === 'approved')
                          <span class="badge diary-pill diary-pill-approved">Đã duyệt</span>
                        @else
                          <span class="badge diary-pill diary-pill-cancelled">Từ chối</span>
                        @endif
                      </td>

                      <td class="text-end">
                        <button wire:click="viewEvent({{ $item->id }})" class="btn btn-sm diary-btn diary-btn-primary" type="button">
                          Chi tiết
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-5">
                        <div class="text-muted">Không có dữ liệu phù hợp.</div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <div class="mt-3 d-flex justify-content-center">
              {{ $events->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TOAST --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
      <div id="apToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body d-flex align-items-start gap-2">
          <div id="apToastIcon" class="ap-toast-ic"></div>
          <div class="flex-grow-1">
            <div id="apToastMsg" class="fw-semibold text-dark"></div>
            <div id="apToastSub" class="small text-muted mt-1"></div>
          </div>
          <button type="button" class="btn-close ms-2 mt-1" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    {{-- MODAL DETAILS --}}
    <div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 diary-modal">
          <div class="modal-header border-0 pb-0">
            <div class="w-100 d-flex align-items-start gap-2">
              <div class="me-auto">
                <h5 class="modal-title fw-bold text-dark mb-1">Chỉnh sửa</h5>
              </div>
              <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>

          <div class="modal-body pt-3">
            @if($selectedEvent)
              <div class="row g-3">
                <div class="col-12 col-md-8">
                  <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                  <input wire:model.defer="edit.title" type="text" class="form-control diary-control">
                  @error('edit.title') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label small fw-semibold text-dark mb-1">Phân loại</label>
                  <select wire:model.defer="edit.category" class="form-select diary-control">
                    <option value="work">Làm việc / nghiên cứu</option>
                    <option value="seminar">Hội thảo / seminar</option>
                    <option value="other">Khác</option>
                  </select>
                  @error('edit.category') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                  <select wire:model.defer="edit.lab_code" class="form-select diary-control">
                    <option value="">Chọn phòng...</option>
                    @foreach($labs as $lab)
                      <option wire:key="lab-edit-{{ $lab->code }}" value="{{ $lab->code }}">{{ $lab->name }} ({{ $lab->code }})</option>
                    @endforeach
                  </select>
                  @error('edit.lab_code') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Bắt đầu</label>
                  <input wire:model.defer="edit.start" type="datetime-local" class="form-control diary-control">
                  @error('edit.start') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Kết thúc</label>
                  <input wire:model.defer="edit.end" type="datetime-local" class="form-control diary-control">
                  @error('edit.end') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                  <select wire:model.defer="edit.status" class="form-select diary-control">
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="cancelled">Từ chối</option>
                  </select>
                  @error('edit.status') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                  <textarea wire:model.defer="edit.description" class="form-control diary-control" rows="3"></textarea>
                  @error('edit.description') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="form-label small fw-semibold text-dark mb-1">Feedback</label>
                  <textarea wire:model.defer="edit.feedback" class="form-control diary-control" rows="3"></textarea>
                  @error('edit.feedback') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <div class="diary-filebox">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                      <div class="fw-bold text-dark">File đính kèm</div>
                      <div class="small text-muted">{{ optional($selectedEvent->files)->count() ?? 0 }} file</div>
                    </div>

                    @if($selectedEvent->files && $selectedEvent->files->count())
                      <div class="diary-filelist">
                        @foreach($selectedEvent->files as $f)
                          @php
                            $p = $f->path ?? $f->file_path ?? $f->url ?? '';
                            $u = $p ? \Illuminate\Support\Facades\Storage::url($p) : '#';
                            $n = $f->name ?? $f->original_name ?? basename((string)$p) ?? 'file';
                          @endphp
                          <a class="diary-fileitem" href="{{ $u }}" target="_blank" rel="noopener">
                            <span class="diary-filedot"></span>
                            <span class="diary-filename">{{ $n }}</span>
                          </a>
                        @endforeach
                      </div>
                    @else
                      <div class="small text-muted">Chưa có file.</div>
                    @endif
                  </div>
                </div>

              </div>
            @endif
          </div>

          <div class="modal-footer border-0 pt-0">
            <div class="d-flex w-100 justify-content-between align-items-center gap-2">
              <button type="button" class="btn diary-btn diary-btn-ghost" data-bs-dismiss="modal">Đóng</button>

              <div class="d-flex gap-2">
                <button wire:click="openDeleteConfirm" type="button" class="btn diary-btn diary-btn-danger">
                  Xóa
                </button>

                <button wire:click="updateEvent" type="button" class="btn diary-btn diary-btn-success">
                  Lưu
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- MODAL CONFIRM --}}
    <div wire:ignore.self class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 diary-modal" style="border-radius: 18px;">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold text-dark mb-1">Xác nhận xóa</h5>
              <div class="small text-muted">Xóa lịch này sẽ không thể khôi phục.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-footer border-0 pt-0">
            <div class="d-flex w-100 justify-content-end gap-2">
              <button type="button" class="btn diary-btn diary-btn-ghost" data-bs-dismiss="modal">Hủy</button>
              <button wire:click="deleteEvent" type="button" class="btn diary-btn diary-btn-danger">Xác nhận xóa</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <style>
    /* ====== SCOPE CSS: chỉ ảnh hưởng trong .diary-page ====== */
    .diary-page{
      --d-bg:#f6f8fc;
      --d-card:#ffffff;
      --d-text:#0f172a;
      --d-muted:#64748b;
      --d-border:#e6eaf2;
      --d-shadow:0 14px 40px rgba(15,23,42,.08);
      --d-radius:18px;
      --d-primary:#2563eb;
      --d-primary-soft:#eaf1ff;
      --d-success:#16a34a;
      --d-success-soft:#e9f9ef;
      --d-danger:#dc2626;
      --d-danger-soft:#ffecec;
      --d-warn:#f59e0b;
      --d-warn-soft:#fff3db;
    }

    .diary-page{ background: var(--d-bg); }

    .diary-page .diary-card{
      border-radius: var(--d-radius);
      background: var(--d-card);
      box-shadow: var(--d-shadow);
      overflow: hidden;
    }

    .diary-page .diary-filters{
      background:#fff;
      border:1px solid var(--d-border);
      border-radius:16px;
      padding:14px;
    }

    .diary-page .diary-control{
      border:1px solid var(--d-border) !important;
      border-radius:12px !important;
      padding:10px 12px !important;
      background:#fff !important;
      box-shadow:none !important;
      color:var(--d-text);
    }
    .diary-page .diary-control:focus{
      border-color: rgba(37,99,235,.35) !important;
      box-shadow: 0 0 0 .2rem rgba(37,99,235,.12) !important;
    }

    .diary-page .diary-table-wrap{
      border:1px solid var(--d-border);
      border-radius:16px;
      overflow:hidden;
      background:#fff;
    }

    .diary-page .diary-table{
      margin:0;
    }

    .diary-page .diary-table thead th{
      background:#fbfcff;
      color:#334155;
      font-weight:700;
      border-bottom:1px solid var(--d-border);
      padding:14px 14px;
      font-size:.92rem;
      vertical-align:middle;
      white-space:nowrap;
    }

    .diary-page .diary-table tbody td{
      border-top:1px solid var(--d-border);
      padding:14px 14px;
      vertical-align:middle;
      color:var(--d-text);
    }

    .diary-page .diary-table tbody tr{
      transition: background .15s ease;
    }
    .diary-page .diary-table tbody tr:hover{
      background: rgba(37,99,235,.03);
    }

    .diary-page .diary-pill{
      border-radius:999px;
      padding:8px 12px;
      font-weight:900;
      border:1px solid transparent;
      font-size:.85rem;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width: 90px;
    }
    .diary-page .diary-pill-pending{
      background: var(--d-warn-soft);
      color:#7a4b00;
      border-color: rgba(245,158,11,.25);
    }
    .diary-page .diary-pill-approved{
      background: var(--d-success-soft);
      color:#0f6a2e;
      border-color: rgba(22,163,74,.22);
    }
    .diary-page .diary-pill-cancelled{
      background: var(--d-danger-soft);
      color:#8a1414;
      border-color: rgba(220,38,38,.22);
    }

    .diary-page .diary-btn{
      border-radius:12px;
      padding:9px 12px;
      font-weight:900;
      border:1px solid transparent;
      transition: transform .06s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
      white-space: nowrap;
    }
    .diary-page .diary-btn:active{ transform: translateY(1px); }

    .diary-page .diary-btn-primary{
      background: var(--d-primary-soft);
      border-color: rgba(37,99,235,.18);
      color: var(--d-primary);
    }
    .diary-page .diary-btn-primary:hover{
      background: rgba(37,99,235,.14);
      border-color: rgba(37,99,235,.25);
    }

    .diary-page .diary-btn-success{
      background: var(--d-success);
      color:#fff;
      border-color: rgba(22,163,74,.4);
      box-shadow: 0 10px 18px rgba(22,163,74,.18);
    }
    .diary-page .diary-btn-success:hover{
      filter: brightness(.98);
      box-shadow: 0 14px 24px rgba(22,163,74,.22);
    }

    .diary-page .diary-btn-danger{
      background: var(--d-danger-soft);
      border-color: rgba(220,38,38,.18);
      color: var(--d-danger);
    }
    .diary-page .diary-btn-danger:hover{
      background: rgba(220,38,38,.12);
      border-color: rgba(220,38,38,.25);
    }

    .diary-page .diary-btn-ghost{
      background:#fff;
      color:#334155;
      border-color: var(--d-border);
      border:1px solid var(--d-border);
    }
    .diary-page .diary-btn-ghost:hover{ background:#f8fafc; }

    /* CHỈ style modal-content có class diary-modal, không đụng modal của m */
    .diary-page .diary-modal{
      border-radius:20px;
      box-shadow:0 18px 48px rgba(15,23,42,.16);
      overflow:hidden;
      background: #fff;
    }

    .diary-page .diary-filebox{
      border:1px solid var(--d-border);
      border-radius:16px;
      padding:12px 14px;
      background:#fff;
    }
    .diary-page .diary-filelist{
      display:flex;
      flex-direction:column;
      gap:10px;
    }
    .diary-page .diary-fileitem{
      display:flex;
      align-items:center;
      gap:10px;
      padding:10px 12px;
      border-radius:12px;
      border:1px solid rgba(15,23,42,.06);
      background:#fbfcff;
      text-decoration:none;
      color: var(--d-text);
      transition: background .12s ease, border-color .12s ease;
    }
    .diary-page .diary-fileitem:hover{
      background:#f3f7ff;
      border-color: rgba(37,99,235,.16);
    }
    .diary-page .diary-filedot{
      width:10px;height:10px;border-radius:99px;
      background: rgba(37,99,235,.35);
      flex:0 0 auto;
    }
    .diary-page .diary-filename{
      font-weight:700;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      width:100%;
    }

    .diary-page .ap-toast-ic{
      width:34px;height:34px;border-radius:12px;
      display:inline-flex;align-items:center;justify-content:center;
      background: rgba(37,99,235,.08);
      border: 1px solid rgba(37,99,235,.14);
      flex: 0 0 auto;
      font-weight: 900;
      color: #0f172a;
    }
  </style>

  @once
  <script>
    function apGetModal(id){
      const el = document.getElementById(id);
      if (!el) return null;
      return bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static' });
    }

    function apToast(type, msg, sub){
      const toastEl = document.getElementById('apToast');
      if (!toastEl || !msg) return;

      const iconEl = document.getElementById('apToastIcon');
      const msgEl = document.getElementById('apToastMsg');
      const subEl = document.getElementById('apToastSub');

      msgEl.textContent = msg || '';
      subEl.textContent = sub || '';

      if (type === 'success') iconEl.textContent = '✓';
      else if (type === 'error') iconEl.textContent = '!';
      else if (type === 'warning') iconEl.textContent = '⚠';
      else iconEl.textContent = 'i';

      bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 2600 }).show();
    }

    function fireSessionToast(){
      const p = document.getElementById('toastPayload');
      if(!p) return;

      const s = p.getAttribute('data-success');
      const e = p.getAttribute('data-error');
      const w = p.getAttribute('data-warning');
      const i = p.getAttribute('data-info');

      if (s) apToast('success', s, '');
      else if (e) apToast('error', e, '');
      else if (w) apToast('warning', w, '');
      else if (i) apToast('info', i, '');

      p.setAttribute('data-success','');
      p.setAttribute('data-error','');
      p.setAttribute('data-warning','');
      p.setAttribute('data-info','');
    }

    window.addEventListener('open-details-modal', () => {
      const m = apGetModal('modalDetails');
      if (m) m.show();
    });

    window.addEventListener('close-details-modal', () => {
      const el = document.getElementById('modalDetails');
      if (!el) return;
      const m = bootstrap.Modal.getInstance(el);
      if (m) m.hide();
    });

    window.addEventListener('open-confirm-modal', () => {
      const m = apGetModal('modalConfirm');
      if (m) m.show();
    });

    window.addEventListener('close-confirm-modal', () => {
      const el = document.getElementById('modalConfirm');
      if (!el) return;
      const m = bootstrap.Modal.getInstance(el);
      if (m) m.hide();
    });

    document.addEventListener('livewire:load', () => {
      fireSessionToast();

      if (window.Livewire && Livewire.hook) {
        Livewire.hook('message.processed', () => {
          fireSessionToast();
        });
      }
    });
  </script>
  @endonce
</div>
