<div>
    <div>
        <div class="page-header page-header-light shadow">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="page-title mb-0">
                        Đăng ký lịch
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
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                            <i class="ph-house"></i>
                        </a>
                        <span class="breadcrumb-item active">Đăng ký lịch </span>
                    </div>

                    <a href="#breadcrumb_elements"
                       class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                       data-bs-toggle="collapse">
                        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4 register-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-8">

                <div class="card border-0 register-card">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Tạo lịch (Admin)</h4>
                                <div class="small text-muted mt-1">Lịch được tạo sẽ tự động duyệt.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="row g-3">
                            {{-- Tiêu đề + phân loại --}}
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                                <input wire:model.defer="form.title"
                                       type="text"
                                       class="form-control register-control"
                                       placeholder="Nhập tiêu đề...">
                                @error('form.title')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-dark mb-1">Phân loại</label>
                                <select wire:model.defer="form.category"
                                        class="form-select register-control">
                                    <option value="work">Làm việc / Nghiên cứu</option>
                                    <option value="seminar">Hội thảo / Seminar</option>
                                    <option value="other">Khác</option>
                                </select>
                                @error('form.category')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phòng, người đăng ký, nhóm --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                <select wire:model.defer="form.lab_code"
                                        class="form-select register-control">
                                    <option value="">Chọn phòng...</option>
                                    @foreach($labs as $lab)
                                        <option wire:key="{{ $lab->code }}"
                                                value="{{ $lab->code }}">
                                            {{ $lab->name }} ({{ $lab->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.lab_code')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Người đăng ký</label>
                                <select wire:model.defer="form.user_id"
                                        class="form-select register-control">
                                    <option value="">Chọn người dùng...</option>
                                    @foreach($users as $u)
                                        <option wire:key="u-{{ $u->id }}" value="{{ $u->id }}">
                                            {{ $u->full_name }}{{ $u->email ? ' • '.$u->email : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.user_id')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Đăng ký cho</label>
                                <select wire:model.defer="form.group_id"
                                        class="form-select register-control">
                                    <option value="">Chọn nhóm / lớp...</option>
                                    @foreach($groups as $g)
                                        <option wire:key="group-{{ $g->id }}" value="{{ $g->id }}">
                                            {{ $g->name ?? ('Group #'.$g->id) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('form.group_id')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Thời gian --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Bắt đầu</label>
                                <input wire:model.defer="form.start"
                                       type="datetime-local"
                                       class="form-control register-control">
                                @error('form.start')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Kết thúc</label>
                                <input wire:model.defer="form.end"
                                       type="datetime-local"
                                       class="form-control register-control">
                                @error('form.end')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Trạng thái --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Trạng thái</label>
                                <select wire:model.defer="form.status"
                                        class="form-select register-control">
                                    <option value="approved">Đã duyệt</option>
                                    <option value="pending">Chờ duyệt</option>
                                    <option value="cancelled">Từ chối</option>
                                </select>
                                @error('form.status')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Màu --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Màu</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input wire:model.defer="form.color"
                                           type="color"
                                           class="form-control form-control-color register-color">
                                    <input wire:model.defer="form.color"
                                           type="text"
                                           class="form-control register-control"
                                           placeholder="#2563eb">
                                </div>
                                @error('form.color')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Lặp lịch --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Lặp lại</label>
                                <select wire:model.defer="form.repeat_type"
                                        class="form-select register-control">
                                    <option value="">Không lặp</option>
                                    <option value="daily">Mỗi ngày</option>
                                    <option value="weekly">Mỗi tuần</option>
                                    <option value="monthly">Mỗi tháng</option>
                                </select>
                                @error('form.repeat_type')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Lặp đến</label>
                                <input wire:model.defer="form.repeat_until"
                                       type="date"
                                       class="form-control register-control">
                                @error('form.repeat_until')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <div class="small text-muted mt-1">
                                    Nếu để trống: chỉ tạo 1 lịch duy nhất.
                                </div>
                            </div>

                            {{-- Mô tả --}}
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                <textarea wire:model.defer="form.description"
                                          class="form-control register-control"
                                          rows="4"
                                          placeholder="Nhập mô tả..."></textarea>
                                @error('form.description')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- File đính kèm + list + xóa --}}
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark mb-1">Tệp đính kèm</label>
                                <input
                                    wire:model="uploads"
                                    type="file"
                                    class="form-control register-control"
                                    multiple
                                    wire:key="upload-input-{{ $uploadIteration }}"
                                >
                                @error('uploads')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @error('uploads.*')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror

                                {{-- @if(!empty($uploads))
                                    <div class="mt-2 register-files">
                                        @foreach($uploads as $i => $f)
                                            <div class="register-file" wire:key="up-{{ $i }}">
                                                <div class="text-truncate">
                                                    <span class="me-2">📎</span>
                                                    {{ method_exists($f, 'getClientOriginalName') ? $f->getClientOriginalName() : 'Tệp' }}
                                                </div>
                                                <button type="button"
                                                        class="btn register-file-remove"
                                                        wire:click="removeUpload({{ $i }})">
                                                    ×
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif --}}
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button wire:click="createEvent"
                                    type="button"
                                    class="btn register-btn register-btn-success">
                                Tạo lịch
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Toast --}}
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
            <div id="apToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex align-items-start gap-2">
                    <div id="apToastIcon" class="ap-toast-ic"></div>
                    <div class="flex-grow-1">
                        <div id="apToastMsg" class="fw-semibold text-dark"></div>
                        <div id="apToastSub" class="small text-muted mt-1"></div>
                    </div>
                    <button type="button"
                            class="btn-close ms-2 mt-1"
                            data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- Modal báo trùng lịch (khi status = approved & có conflict) --}}
         <div wire:ignore.self class="modal fade" id="modalConflict" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 register-card">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark mb-1">⚠️ Cảnh báo trùng khung giờ</h5>
                        <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <div class="alert alert-warning border-0 mb-3" style="background: rgba(234, 179, 8, 0.1); border-radius: 12px;">
                            <div class="small text-dark mb-2">
                                Khung giờ bạn chọn <strong>bị trùng</strong> với một lịch <b>đã duyệt</b> trong cùng phòng lab.
                            </div>
                        </div>
                        
                        <div class="small text-muted mb-2 fw-semibold">Bạn có thể:</div>
                        <ul class="small text-muted ps-3 mb-3">
                            <li class="mb-1">Đổi phòng hoặc đổi khung giờ khác</li>
                            <li class="mb-1">Chuyển trạng thái sang <b>"Chờ duyệt"</b> rồi tạo lại</li>
                            <li class="mb-0">Hoặc nhấn <b>"Vẫn Duyệt"</b> bên dưới để bỏ qua cảnh báo</li>
                        </ul>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button"
                                class="btn register-btn register-btn-ghost"
                                data-bs-dismiss="modal">
                            Hủy
                        </button>
                        <button type="button"
                                class="btn register-btn register-btn-warning"
                                wire:click="createEvent"
                                data-bs-dismiss="modal">
                            Vẫn Duyệt
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        .register-page{
            --r-bg:#f6f8fc;
            --r-card:#fff;
            --r-text:#0f172a;
            --r-border:#e6eaf2;
            --r-shadow:0 14px 40px rgba(15,23,42,.08);
            --r-radius:18px;
            --r-success:#16a34a;
        }
        .register-card{
            border-radius: var(--r-radius);
            background: var(--r-card);
            box-shadow: var(--r-shadow);
            overflow:hidden;
        }
        .register-control{
            border:1px solid var(--r-border) !important;
            border-radius:12px !important;
            padding:10px 12px !important;
            background:#fff !important;
            box-shadow:none !important;
            color: var(--r-text);
        }
        .register-control:focus{
            border-color: rgba(37,99,235,.35) !important;
            box-shadow: 0 0 0 .2rem rgba(37,99,235,.12) !important;
        }
        .register-btn{
            border-radius:12px;
            padding:9px 12px;
            font-weight:900;
            border:1px solid transparent;
            white-space:nowrap;
        }
        .register-btn-success{
            background: var(--r-success);
            color:#fff;
            border-color: rgba(22,163,74,.4);
            box-shadow:0 10px 18px rgba(22,163,74,.18);
        }
        .register-btn-ghost{
            background:#fff;
            color:#334155;
            border:1px solid var(--r-border);
        }
        .register-btn-ghost:hover{
            background:#f8fafc;
        }
        .register-color{
            width: 46px;
            height: 42px;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid var(--r-border);
            background: #fff;
            flex: 0 0 auto;
        }
        .register-files{
            display:grid;
            grid-template-columns: 1fr;
            gap:10px;
        }
        .register-file{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            border:1px solid var(--r-border);
            border-radius:14px;
            padding:10px 12px;
            background:#fff;
        }
        .register-file-remove{
            width:30px;
            height:30px;
            border-radius:10px;
            border:1px solid rgba(220,38,38,.18);
            background: rgba(220,38,38,.08);
            color:#dc2626;
            font-weight:900;
            line-height: 1;
        }
        .register-file-remove:hover{
            background: rgba(220,38,38,.12);
        }
        .ap-toast-ic{
            width:34px;
            height:34px;
            border-radius:12px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background: rgba(37,99,235,.08);
            border:1px solid rgba(37,99,235,.14);
            flex:0 0 auto;
            font-weight:900;
        }
        @media (min-width: 992px){
            .register-files{
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    @once
        <script>
            function apToast(type, msg, sub){
                const toastEl = document.getElementById('apToast');
                if (!toastEl) return;

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

            window.addEventListener('toast', (e) => {
                const d = (e && e.detail) ? e.detail : {};
                apToast(d.type || 'info', d.message || '', d.sub || '');
            });

            // Modal trùng lịch cho register
            window.addEventListener('open-conflict-modal', () => {
                const el = document.getElementById('modalConflict');
                if (!el) return;
                bootstrap.Modal.getOrCreateInstance(el).show();
            });
        </script>
    @endonce
</div>
