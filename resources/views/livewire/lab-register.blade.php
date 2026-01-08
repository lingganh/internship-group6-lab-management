<div>
    {{-- HEADER --}}
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">Đăng ký lịch</h4>
            </div>
        </div>
        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                        <i class="ph-house"></i>
                    </a>
                    <span class="breadcrumb-item active">Đăng ký lịch</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="container-fluid py-4 register-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                <div class="card border-0 register-card">
                    <div class="card-header bg-white border-0 pb-0">
                        <h4 class="fw-bold text-dark mb-0">Tạo lịch (Admin)</h4>
                        <div class="small text-muted mt-1">Lịch được tạo sẽ tự động duyệt.</div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="row g-4">
                            {{-- CỘT TRÁI: Thông tin lịch --}}
                            <div class="col-12 col-md-6">
                                <div class="vstack gap-3">

                                    {{-- Tiêu đề --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                                        <input wire:model.defer="form.title"
                                               type="text"
                                               class="form-control register-control"
                                               placeholder="Nhập tiêu đề...">
                                        @error('form.title')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Hoạt động --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Hoạt động</label>
                                        <select wire:model.defer="form.category"
                                                class="form-select register-control">
                                            <option value="work">Làm việc - Nghiên cứu</option>
                                            <option value="seminar">Hội thảo - Seminar</option>
                                            <option value="other">Khác</option>
                                        </select>
                                        @error('form.category')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Phòng lab --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Phòng lab</label>
                                        <select wire:model.defer="form.lab_code"
                                                class="form-select register-control">
                                            <option value="">Chọn phòng lab...</option>
                                            @foreach($labs as $lab)
                                                <option value="{{ $lab['code'] }}">
                                                    {{ $lab['name'] }} ({{ $lab['code'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('form.lab_code')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Người đăng ký + Nhóm --}}
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">Người đăng ký</label>
                                            <select wire:model.defer="form.user_id"
                                                    class="form-select register-control">
                                                <option value="">Chọn người dùng...</option>
                                                @foreach($users as $u)
                                                    <option value="{{ $u->id }}">
                                                        {{ $u->full_name }}{{ $u->email ? ' • '.$u->email : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('form.user_id')
                                                <div class="small text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-label small fw-semibold text-dark mb-1">Nhóm</label>
                                            <select wire:model.defer="form.group_id"
                                                    class="form-select register-control">
                                                <option value="">Chọn nhóm / lớp...</option>
                                                @foreach($groups as $g)
                                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('form.group_id')
                                                <div class="small text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Trạng thái --}}
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
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
                                    </div>

                                    {{-- Mô tả --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                        <textarea wire:model.defer="form.description"
                                                  rows="4"
                                                  class="form-control register-control"
                                                  placeholder="Nhập mô tả..."></textarea>
                                        @error('form.description')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            {{-- CỘT PHẢI: Thời gian + Lặp + File --}}
                            <div class="col-12 col-md-6">
                                <div class="vstack gap-3 sticky-col">

                                    {{-- Thời gian --}}
                                    <div class="time-section">
                                        <div class="time-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M12 6v6l4 2"/>
                                            </svg>
                                            <span>Thời gian</span>
                                        </div>
                                        <div class="row g-3 align-items-end">
                                            <div class="col-12 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Ngày</label>
                                                <input type="date"
                                                       wire:model.live="eventDate"
                                                       class="form-control register-control">
                                                @if(!empty($eventDate))
                                                    @php
                                                        $d = \Carbon\Carbon::parse($eventDate);
                                                        $dowMap = [
                                                            0 => 'Chủ nhật',
                                                            1 => 'Thứ hai',
                                                            2 => 'Thứ ba',
                                                            3 => 'Thứ tư',
                                                            4 => 'Thứ năm',
                                                            5 => 'Thứ sáu',
                                                            6 => 'Thứ bảy',
                                                        ];
                                                    @endphp
                                                    {{-- <div class="small text-muted mt-1">
                                                        Ngày đã chọn:
                                                        <strong>{{ $dowMap[$d->dayOfWeek] }}, {{ $d->format('d/m/Y') }}</strong>
                                                    </div> --}}
                                                @endif
                                            </div>
                                            <div class="col-6 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Giờ bắt đầu</label>
                                                <input type="time"
                                                       wire:model.live="startTime"
                                                       class="form-control register-control">
                                            </div>
                                            <div class="col-6 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Giờ kết thúc</label>
                                                <input type="time"
                                                       wire:model.live="endTime"
                                                       class="form-control register-control">
                                            </div>
                                            <div class="col-12 text-end small text-muted">
                                                <b>{{ $totalDuration }}</b>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Lặp --}}
                                    <div class="repeat-section">
                                        <div class="repeat-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2">
                                                <path d="M17 1l4 4-4 4"/>
                                                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                                                <path d="M7 23l-4-4 4-4"/>
                                                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                                            </svg>
                                            <span>Lặp lại</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1">Tần suất</label>
                                                <select wire:model.live="form.repeat_type"
                                                        class="form-select register-control">
                                                    <option value="">Không lặp</option>
                                                    <option value="daily">Hàng ngày</option>
                                                    <option value="weekly">Hàng tuần</option>
                                                    <option value="monthly">Hàng tháng</option>
                                                </select>
                                                @error('form.repeat_type')
                                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1">Lặp đến ngày</label>
                                                <input type="date"
                                                       wire:model.live="form.repeat_until"
                                                       class="form-control register-control">
                                                @error('form.repeat_until')
                                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                                @if(!empty($form['repeat_until']))
                                                    @php
                                                        $ru = \Carbon\Carbon::parse($form['repeat_until']);
                                                        $dowMap2 = [
                                                            0 => 'Chủ nhật',
                                                            1 => 'Thứ hai',
                                                            2 => 'Thứ ba',
                                                            3 => 'Thứ tư',
                                                            4 => 'Thứ năm',
                                                            5 => 'Thứ sáu',
                                                            6 => 'Thứ bảy',
                                                        ];
                                                    @endphp
                                                    {{-- <div class="small text-muted mt-1">
                                                        Lặp đến:
                                                        <strong>{{ $dowMap2[$ru->dayOfWeek] }}, {{ $ru->format('d/m/Y') }}</strong>
                                                    </div> --}}
                                                @endif
                                                <div class="small text-muted mt-1">Để trống: chỉ tạo 1 lịch.</div>
                                            </div>

                                            @if(($form['repeat_type'] ?? '') === 'weekly')
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold text-dark mb-2">
                                                        Chọn ngày lặp lại trong tuần
                                                        @if(!empty($repeatDays))
                                                            <span class="text-muted fw-normal">
                                                                (Tổng số tuần: {{ (int) $totalWeeks }})
                                                            </span>
                                                        @endif
                                                    </label>
                                                    <div class="weekday-selector">
                                                        @php
                                                            $days = [
                                                                1 => 'Thứ hai',
                                                                2 => 'Thứ ba',
                                                                3 => 'Thứ tư',
                                                                4 => 'Thứ năm',
                                                                5 => 'Thứ sáu',
                                                                6 => 'Thứ bảy',
                                                                0 => 'Chủ nhật',
                                                            ];
                                                        @endphp
                                                        @foreach($days as $val => $label)
                                                            <label class="weekday-btn">
                                                                <input type="checkbox"
                                                                       value="{{ $val }}"
                                                                       wire:model.live="repeatDays">
                                                                <span>{{ $label }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @error('repeatDays')
                                                        <div class="small text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tệp đính kèm --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Tệp đính kèm</label>
                                        <input wire:model="uploads"
                                               type="file"
                                               multiple
                                               class="form-control register-control"
                                               wire:key="upload-input-{{ $uploadIteration }}">
                                        @error('uploads')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        @error('uploads.*')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        @if(!empty($uploads))
                                            <div class="small text-muted mt-2">
                                                Đã chọn {{ count($uploads) }} tệp.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Nút lưu --}}
                                    <div class="d-flex justify-content-end">
                                        <button wire:click="createEvent"
                                                type="button"
                                                class="btn register-btn register-btn-success">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" class="me-2">
                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                            </svg>
                                            Lưu
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- MODAL xung đột --}}
        <div wire:ignore.self class="modal fade" id="modalConflict" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 register-card">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark mb-1">⚠️ Cảnh báo trùng khung giờ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <div class="alert alert-warning border-0 mb-3"
                             style="background:rgba(234,179,8,.1);border-radius:12px;">
                            <div class="small text-dark mb-2">
                                Khung giờ bạn chọn <strong>bị trùng</strong> với một lịch <b>đã duyệt</b> trong cùng phòng lab.
                            </div>
                        </div>
                        <ul class="small text-muted ps-3 mb-3">
                            <li>Đổi phòng hoặc đổi khung giờ khác</li>
                            <li>Chuyển trạng thái sang <b>"Chờ duyệt"</b> rồi tạo lại</li>
                            <li>Hoặc nhấn <b>"Vẫn Duyệt"</b> bên dưới để bỏ qua cảnh báo</li>
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
                                wire:click="forceApprove"
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
            --r-border:#e6eaf2;
            --r-text:#0f172a;
            --r-success:#16a34a;
            --r-primary:#2563eb;
        }
        .register-card{
            border-radius:18px;
            box-shadow:0 14px 40px rgba(15,23,42,.08);
        }
        .register-control{
            border:1px solid var(--r-border)!important;
            border-radius:12px!important;
            padding:10px 12px!important;
        }
        .register-btn{
            border-radius:12px;
            padding:10px 18px;
            font-weight:600;
            display:inline-flex;
            align-items:center;
            border:1px solid transparent;
        }
        .register-btn-success{
            background:var(--r-success);
            color:#fff;
        }
        .register-btn-warning{
            background:#f59e0b;
            color:#fff;
            border-color:rgba(245,158,11,.4);
        }
        .register-btn-ghost{
            background:#fff;
            color:#334155;
            border:1px solid var(--r-border);
        }
        .time-section{
            background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%);
            border:1px solid var(--r-border);
            border-radius:16px;
            padding:20px;
        }
        .time-header{
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:600;
            color:var(--r-text);
            margin-bottom:16px;
            font-size:15px;
        }
        .repeat-section{
            background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);
            border:1px solid #fbbf24;
            border-radius:16px;
            padding:20px;
        }
        .repeat-header{
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:600;
            color:#92400e;
            margin-bottom:16px;
            font-size:15px;
        }
        .weekday-selector{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(110px,1fr));
            gap:10px;
        }
        .weekday-btn{
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:12px 16px;
            background:#fff;
            border:2px solid var(--r-border);
            border-radius:12px;
            cursor:pointer;
            transition:all .2s ease;
            font-size:14px;
            font-weight:500;
            color:#64748b;
        }
        .weekday-btn input{
            position:absolute;
            opacity:0;
            pointer-events:none;
        }
        .weekday-btn:hover{
            border-color:var(--r-primary);
            background:#f8fafc;
        }
        .weekday-btn:has(input:checked){
            background:var(--r-primary);
            border-color:var(--r-primary);
            color:#fff;
            box-shadow:0 4px 12px rgba(37,99,235,.25);
        }
        .sticky-col{
            position:sticky;
            top:84px;
        }
        @media (max-width:991.98px){
            .sticky-col{
                position:static;
                top:auto;
            }
        }
    </style>

    @once
        <script>
            // chỉ mở modal, toast dùng event 'alert' global sẵn của m
            window.addEventListener('open-conflict-modal', () => {
                const el = document.getElementById('modalConflict');
                if (!el) return;
                bootstrap.Modal.getOrCreateInstance(el).show();
            });
        </script>
    @endonce
</div>
