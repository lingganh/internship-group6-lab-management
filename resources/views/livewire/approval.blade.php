<div>
   <div>
   <div>
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Duyệt lịch đăng ký
                    </h4>

                    <a href="#page_header"
                       class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                       data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>
            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="breadcrumb py-2">
                        <a href="{{route('admin.dashboard')}}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <span class="breadcrumb-item active">Duyệt lịch đăng ký </span>
                    </div>

                    <a href="#breadcrumb_elements" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>

  <div class="container-fluid py-4 approval-page">
    <div class="row justify-content-center">
      <div class="col-12 col-xxl-11">
        <div class="card border-0 approval-card">
          <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
              <div>
                <h4 class="mb-1 fw-bold text-dark">Phê duyệt lịch đăng ký</h4>
                <div class="text-muted small">Quản lý các yêu cầu đăng ký phòng lab theo trạng thái / phòng / người dùng
                  / ngày.</div>
              </div>

              <div class="d-flex align-items-center gap-2">
                <span class="badge approval-badge-warn">
                  <span class="me-1">Đang chờ</span>
                  <span class="fw-bold">{{ $pendingCount }}</span>
                </span>
              </div>
            </div>
          </div>

          <div class="card-body pt-3">
            <div class="approval-filters mb-3">
              <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                  <select wire:model.live="filterStatus" class="form-select approval-control">
                    <option value="pending">Chờ phê duyệt</option>
                    <option value="approved">Đã phê duyệt</option>
                    <option value="cancelled">Đã từ chối</option>
                    <option value="completed">Đã hoàn thành</option>
                    <option value="">Tất cả</option>
                  </select>
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                  <select wire:model.live="filterLabCode" class="form-select approval-control">
                    <option value="">Tất cả phòng</option>
                    @foreach($labs as $lab)
                      <option value="{{ $lab->code }}">{{ $lab->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Người đăng ký</label>
                  <select wire:model.live="filterUserId" class="form-select approval-control">
                    <option value="">Tất cả người dùng</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label small fw-semibold text-dark mb-1">Ngày đăng ký</label>
                  <input type="date" wire:model.live="filterDate" class="form-control approval-control">
                </div>
              </div>
            </div>

            <div class="table-responsive approval-table-wrap">
              <table class="table align-middle mb-0 approval-table">
                <thead>
                  <tr>
                    <th style="min-width: 260px;">Sự kiện</th>
                    <th style="min-width: 200px;">Phòng</th>
                    <th style="min-width: 180px;">Người đăng ký</th>
                    <th style="min-width: 190px;">Thời gian</th>
                    <th class="text-center" style="width: 150px;">Trạng thái</th>
                    <th class="text-end" style="width: 260px;">Hành động</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse($schedules as $item)
                    <tr>
                      <td>
                        <div class="d-flex flex-column">
                          <div class="fw-semibold text-dark text-truncate" style="max-width: 360px;">
                            {{ $item->title }}
                          </div>
                          <div class="small text-muted d-flex align-items-center gap-2">
                            <span class="approval-dot"></span>
                            <span class="text-truncate" style="max-width: 360px;">{{ $item->category }}</span>
                          </div>
                        </div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark text-truncate" style="max-width: 240px;">
                          {{ $item->lab?->name ?? ($item->lab_code ?? 'N/A') }}
                        </div>
                        <div class="small text-muted">Mã: {{ $item->lab_code ?? '-' }}</div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark">{{ $item->user?->full_name ?? 'N/A' }}</div>
                        <div class="small text-muted">ID: {{ $item->user_id ?? '-' }}</div>
                      </td>

                      <td>
                        <div class="fw-semibold text-dark">{{ optional($item->start)->format('d/m/Y') }}</div>
                        <div class="small text-muted">{{ optional($item->start)->format('H:i') }} –
                          {{ optional($item->end)->format('H:i') }}</div>
                      </td>

                      <td class="text-center">
                        @if($item->status === 'pending')
                          <span class="badge approval-pill approval-pill-pending">Chờ duyệt</span>
                        @elseif($item->status === 'approved')
                          <span class="badge approval-pill approval-pill-approved">Đã duyệt</span>
                        @elseif($item->status === 'completed')
                          <span class="badge approval-pill approval-pill-approved">Đã hoàn thành</span>
                        @else
                          <span class="badge approval-pill approval-pill-cancelled">Từ chối</span>
                        @endif
                      </td>

                      <td class="text-end">
                        <div class="d-inline-flex gap-2">
                          <button wire:click="viewSchedule({{ $item->id }})"
                            class="btn btn-sm approval-btn approval-btn-primary" type="button">
                            Chi tiết
                          </button>

                          @if($item->status === 'pending')
                            <button wire:click="approveNow({{ $item->id }})"
                              class="btn btn-sm approval-btn approval-btn-success" type="button">
                              Phê duyệt
                            </button>
                            <button wire:click="confirmReject({{ $item->id }})"
                              class="btn btn-sm approval-btn approval-btn-danger" type="button">
                              Từ chối
                            </button>
                          @endif
                        </div>
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
              {{ $schedules->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>


    {{-- ======= MODAL CHI TIẾT ======= --}}
    <div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 approval-modal">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold text-dark mb-1">Chi tiết đăng ký</h5>
              <div class="small text-muted">Xem thông tin và xử lý yêu cầu (nếu đang chờ).</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body pt-3">
            @if($selectedSchedule)
              <div class="row g-3">

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Tiêu đề / Phân loại</div>
                    <div class="fw-semibold text-dark">{{ $selectedSchedule->title }}</div>
                    <div class="small text-muted mt-1">{{ $selectedSchedule->category }}</div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Phòng lab</div>
                    <div class="fw-semibold text-dark">
                      {{ $selectedSchedule->lab?->name ?? ($selectedSchedule->lab_code ?? 'N/A') }}
                    </div>
                    <div class="small text-muted mt-1">Mã: {{ $selectedSchedule->lab_code ?? '-' }}</div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Người đăng ký</div>
                    <div class="fw-semibold text-dark">{{ $selectedSchedule->user?->full_name ?? 'N/A' }}</div>
                    <div class="small text-muted mt-1">{{ $selectedSchedule->user?->email }}</div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Đăng ký cho nhóm (nếu có)</div>
                    <div class="fw-semibold text-dark">
                      {{ $selectedSchedule->group?->name ?? 'N/A' }}
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Trạng thái</div>
                    <div class="fw-semibold text-dark">
                      @if($selectedSchedule->status === 'pending') Chờ duyệt
                      @elseif($selectedSchedule->status === 'approved') Đã duyệt
                      @else Từ chối
                      @endif
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Thời gian</div>
                    <div class="fw-semibold text-dark">
                      {{ optional($selectedSchedule->start)->format('H:i d/m/Y') }}
                      <span class="text-muted">—</span>
                      {{ optional($selectedSchedule->end)->format('H:i d/m/Y') }}
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="approval-info">
                    <div class="small text-muted mb-1">Mô tả</div>
                    <div class="approval-desc">{{ $selectedSchedule->description ?? 'Không có mô tả.' }}</div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="approval-info">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="small text-muted fw-semibold">
                        Tệp đính kèm ({{ $selectedSchedule->files->count() }})
                      </div>
                    </div>

                    <div class="mt-2 approval-files">
                      @forelse($selectedSchedule->files as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="approval-file">
                          <span class="approval-file-ic">📄</span>
                          <span class="text-truncate">{{ $file->file_name }}</span>
                        </a>
                      @empty
                        <div class="small text-muted approval-empty">Không có tệp kèm theo.</div>
                      @endforelse
                    </div>
                  </div>
                </div>

              </div>
            @endif
          </div>

          <div class="modal-footer border-0 pt-0">
            <div class="d-flex w-100 justify-content-between align-items-center gap-2">
              <button type="button" class="btn approval-btn approval-btn-ghost" data-bs-dismiss="modal">Đóng</button>

              @if($selectedSchedule && $selectedSchedule->status === 'pending')
                <div class="d-flex gap-2">
                  <button wire:click="confirmReject({{ $selectedSchedule->id }})" type="button"
                    class="btn approval-btn approval-btn-danger">
                    Từ chối
                  </button>
                  <button wire:click="approveNow({{ $selectedSchedule->id }})" type="button"
                    class="btn approval-btn approval-btn-success">
                    Phê duyệt
                  </button>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>


    {{-- ======= MODAL TỪ CHỐI – NHẬP LÝ DO ======= --}}
    <div wire:ignore.self class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 approval-modal" style="border-radius: 18px;">
          
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold text-dark mb-1">{{ $confirmTitle }}</h5>
              <div class="small text-muted">{{ $confirmMessage }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-3">
            <div class="approval-info">
              <label class="form-label small fw-semibold text-dark mb-2">Lý do từ chối</label>

              <textarea
                wire:model.defer="rejectionNote"
                class="form-control approval-control"
                rows="3"
                placeholder="Nhập lý do từ chối..."
              ></textarea>

              <div class="small text-muted mt-2">
                💡 Lý do này sẽ được gửi cho người đăng ký.
              </div>

              @error('rejectionNote')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <div class="d-flex w-100 justify-content-end gap-2">
              <button type="button" class="btn approval-btn approval-btn-ghost" data-bs-dismiss="modal">Hủy</button>
              <button wire:click="performConfirm" type="button" class="btn approval-btn approval-btn-danger">
                Xác nhận từ chối
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>


    {{-- ======= MODAL MẬT KHẨU PHÒNG ======= --}}
    <div wire:ignore.self class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 approval-modal" style="border-radius: 18px;">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold text-dark mb-1">🔑 Nhập mật khẩu phòng</h5>
              <div class="small text-muted">Mật khẩu sẽ được gửi qua email cho người dùng</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-3">
            <div class="approval-info">
              <label class="form-label small fw-semibold text-dark mb-2">Mật khẩu phòng lab</label>
              <input type="text" wire:model="roomPassword" class="form-control approval-control"
                placeholder="Nhập mật khẩu..." autofocus>
              <div class="small text-muted mt-2">
                💡 Mật khẩu này sẽ được gửi qua email cho người đăng ký
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 pt-0">
            <div class="d-flex w-100 justify-content-end gap-2">
              <button type="button" class="btn approval-btn approval-btn-ghost" data-bs-dismiss="modal">Hủy</button>
              <button wire:click="approveSchedule" type="button"
                class="btn approval-btn approval-btn-success">
                Xác nhận phê duyệt
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>



  {{-- ========= JS =========== --}}
  <script>
    function apGetModal(id) {
      const el = document.getElementById(id);
      if (!el) return null;
      return bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static' });
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

    window.addEventListener('open-password-modal', () => {
      const m = apGetModal('modalPassword');
      if (m) m.show();
    });

    window.addEventListener('close-password-modal', () => {
      const el = document.getElementById('modalPassword');
      if (!el) return;
      const m = bootstrap.Modal.getInstance(el);
      if (m) m.hide();
    });
  </script>

</div>

  <style>
    .approval-page {
      --ap-bg: #f6f8fc;
      --ap-card: #ffffff;
      --ap-text: #0f172a;
      --ap-muted: #64748b;
      --ap-border: #e6eaf2;
      --ap-shadow: 0 14px 40px rgba(15, 23, 42, .08);
      --ap-radius: 18px;
      --ap-primary: #2563eb;
      --ap-primary-soft: #eaf1ff;
      --ap-success: #16a34a;
      --ap-success-soft: #e9f9ef;
      --ap-danger: #dc2626;
      --ap-danger-soft: #ffecec;
      --ap-warn: #f59e0b;
      --ap-warn-soft: #fff3db;
    }

    /* body{ background: var(--ap-bg); } */

    .approval-card {
      border-radius: var(--ap-radius);
      background: var(--ap-card);
      box-shadow: var(--ap-shadow);
      overflow: hidden;
    }

    .approval-badge-warn {
      background: var(--ap-warn-soft);
      color: #7a4b00;
      border: 1px solid rgba(245, 158, 11, .22);
      border-radius: 999px;
      padding: 10px 14px;
      font-weight: 600;
      letter-spacing: .2px;
    }

    .approval-filters {
      background: #fff;
      border: 1px solid var(--ap-border);
      border-radius: 16px;
      padding: 14px;
    }

    .approval-control {
      border: 1px solid var(--ap-border) !important;
      border-radius: 12px !important;
      padding: 10px 12px !important;
      background: #fff !important;
      box-shadow: none !important;
      color: var(--ap-text);
    }

    .approval-control:focus {
      border-color: rgba(37, 99, 235, .35) !important;
      box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12) !important;
    }

    .approval-table-wrap {
      border: 1px solid var(--ap-border);
      border-radius: 16px;
      overflow: hidden;
      background: #fff;
    }

    .approval-table thead th {
      background: #fbfcff;
      color: #334155;
      font-weight: 700;
      border-bottom: 1px solid var(--ap-border);
      padding: 14px 14px;
      font-size: .92rem;
    }

    .approval-table tbody td {
      border-top: 1px solid var(--ap-border);
      padding: 14px 14px;
      vertical-align: middle;
      color: var(--ap-text);
    }

    .approval-table tbody tr {
      transition: background .15s ease;
    }

    .approval-table tbody tr:hover {
      background: rgba(37, 99, 235, .03);
    }

    .approval-dot {
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: rgba(37, 99, 235, .35);
      display: inline-block;
    }

    .approval-pill {
      border-radius: 999px;
      padding: 8px 12px;
      font-weight: 700;
      border: 1px solid transparent;
      font-size: .85rem;
    }

    .approval-pill-pending {
      background: var(--ap-warn-soft);
      color: #7a4b00;
      border-color: rgba(245, 158, 11, .25);
    }

    .approval-pill-approved {
      background: var(--ap-success-soft);
      color: #0f6a2e;
      border-color: rgba(22, 163, 74, .22);
    }

    .approval-pill-cancelled {
      background: var(--ap-danger-soft);
      color: #8a1414;
      border-color: rgba(220, 38, 38, .22);
    }

    .approval-btn {
      border-radius: 12px;
      padding: 9px 12px;
      font-weight: 700;
      border: 1px solid transparent;
      transition: transform .06s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
    }

    .approval-btn:active {
      transform: translateY(1px);
    }

    .approval-btn-primary {
      background: var(--ap-primary-soft);
      border-color: rgba(37, 99, 235, .18);
      color: var(--ap-primary);
    }

    .approval-btn-primary:hover {
      background: rgba(37, 99, 235, .14);
      border-color: rgba(37, 99, 235, .25);
    }

    .approval-btn-danger {
      background: var(--ap-danger-soft);
      border-color: rgba(220, 38, 38, .18);
      color: var(--ap-danger);
    }

    .approval-btn-danger:hover {
      background: rgba(220, 38, 38, .12);
      border-color: rgba(220, 38, 38, .25);
    }

    .approval-btn-success {
      background: var(--ap-success);
      color: #fff;
      border-color: rgba(22, 163, 74, .4);
      box-shadow: 0 10px 18px rgba(22, 163, 74, .18);
    }

    .approval-btn-success:hover {
      filter: brightness(.98);
    }

    .approval-btn-ghost {
      background: #fff;
      color: #334155;
      border-color: var(--ap-border);
    }

    .approval-btn-ghost:hover {
      background: #f8fafc;
    }

    .approval-modal {
      border-radius: 20px;
      box-shadow: 0 18px 48px rgba(15, 23, 42, .16);
      overflow: hidden;
    }

    .approval-info {
      border: 1px solid var(--ap-border);
      border-radius: 16px;
      padding: 12px 14px;
      background: #fff;
    }

    .approval-desc {
      background: #fbfcff;
      border: 1px solid var(--ap-border);
      border-radius: 14px;
      padding: 12px 12px;
      color: #0f172a;
      font-size: .95rem;
      line-height: 1.5;
      white-space: pre-line;
    }

    .approval-files {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .approval-file {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 14px;
      border: 1px solid var(--ap-border);
      background: #fff;
      text-decoration: none;
      color: #0f172a;
      transition: background .12s ease, border-color .12s ease;
    }

    .approval-file:hover {
      background: rgba(37, 99, 235, .04);
      border-color: rgba(37, 99, 235, .22);
      color: #0f172a;
    }

    .approval-file-ic {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(37, 99, 235, .08);
      border: 1px solid rgba(37, 99, 235, .14);
      flex: 0 0 auto;
    }

    .approval-empty {
      background: #fbfcff;
      border: 1px dashed var(--ap-border);
      border-radius: 14px;
      padding: 12px 12px;
    }

    .ap-toast-ic {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(37, 99, 235, .08);
      border: 1px solid rgba(37, 99, 235, .14);
      flex: 0 0 auto;
      font-weight: 800;
    }

    @media (min-width: 992px) {
      .approval-files {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>

 </div>   