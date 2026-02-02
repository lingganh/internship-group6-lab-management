<div>
    {{-- CSRF Token Meta --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

    {{-- PAGE --}}
    <div class="lab-calendar-page">
        <div class="lab-calendar-shell">
            <div class="lab-layout">
                {{-- SIDEBAR --}}
                <aside class="lab-sidebar">
                    @auth
                        <button type="button" class="create-btn js-open-create-event">
                            <i class="fa-solid fa-plus"></i>
                            <span>Tạo sự kiện</span>
                        </button>
                    @endauth

                    <div class="lab-mini-calendar" id="miniCalendar"></div>

{{--                    <div class="lab-checklist">--}}
{{--                        <label class="lab-check-item">--}}
{{--                            <input type="checkbox" checked data-filter-status="pending">--}}
{{--                            <span class="custom-checkbox" style="--cb-color: #f59e0b;"></span>--}}
{{--                            <span class="lab-check-label">Chờ duyệt</span>--}}
{{--                        </label>--}}

{{--                        <label class="lab-check-item">--}}
{{--                            <input type="checkbox" checked data-filter-status="approved">--}}
{{--                            <span class="custom-checkbox" style="--cb-color: #10b981;"></span>--}}
{{--                            <span class="lab-check-label">Đã duyệt</span>--}}
{{--                        </label>--}}

{{--                        <label class="lab-check-item">--}}
{{--                            <input type="checkbox" checked data-filter-status="completed">--}}
{{--                            <span class="custom-checkbox" style="--cb-color: #6366f1;"></span>--}}
{{--                            <span class="lab-check-label">Đã hoàn thành</span>--}}
{{--                        </label>--}}
{{--                    </div>--}}
                        <div class="lab-checklist">
                            @foreach($statuses as $status)
                                <label class="lab-check-item">
                                    <input type="checkbox" checked data-filter-status="{{ $status->code }}">
                                    <span class="custom-checkbox" style="--cb-color: {{ $status->color }};"></span>
                                    <span class="lab-check-label">{{ $status->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="lab-sidebar-section">
                            <div class="lab-sidebar-section-title">Loại sự kiện</div>
                            <div class="lab-checklist">
                                @foreach($categories as $category)
                                    <label class="lab-check-item">
                                        <input type="checkbox" checked data-filter-category="{{ $category->code }}">
                                        <span class="custom-checkbox" style="--cb-color:blue;"></span>

                                         <span class="lab-check-label">
                                            {{ $category->name }}
                                             <i class="fa-solid fa-{{ $category->icon }}" style="margin-right: 6px;"></i>

                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                </aside>

                {{-- MAIN CALENDAR --}}
                <div class="lab-calendar-main">
                    <div class="lab-calendar-card">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TẠO / SỬA LỊCH --}}
    @auth
        <div id="eventModal" class="lab-modal">
            <div class="lab-modal-content register-card">
                {{-- Header --}}
                <div class="modal-header bg-white border-0 pb-0 px-4 pt-3">
                    <h4 class="fw-bold text-dark mb-0" id="modalTitle">Tạo lịch</h4>
                    <button type="button" class="btn btn-sm btn-light" onclick="closeModal()">
                        <i class="ph-x"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body pt-3 px-4 pb-4 register-page">
                    <form id="eventForm">
                        <input type="hidden" id="eventId">

                        <div class="row g-4">
                            {{-- CỘT TRÁI --}}
                            <div class="col-12 col-md-6">
                                <div class="vstack gap-3">
                                    {{-- Tiêu đề --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề</label>
                                        <input type="text" id="eventTitle" class="form-control register-control"
                                            placeholder="Nhập tiêu đề..." required>
                                    </div>

                                    {{-- Hoạt động --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Hoạt động</label>
                                        <select id="eventCategory" class="form-select register-control" required>

                                            <option value="work">Làm việc - Nghiên cứu</option>
                                            <option value="seminar">Hội thảo - Seminar</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>

                                    {{-- Nhóm / lớp --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Nhóm / lớp</label>
                                        <select id="eventRegisteredFor" class="form-select register-control">
                                            <option value="">Chọn nhóm / lớp...</option>
                                            @foreach($groups as $g)
                                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Mô tả --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                        <textarea id="eventDescription" rows="4" class="form-control register-control"
                                            placeholder="Nhập mô tả..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- CỘT PHẢI --}}
                            <div class="col-12 col-md-6">
                                <div class="vstack gap-3 sticky-col">
                                    {{-- Thời gian --}}
                                    <div class="time-section">
                                        <div class="time-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 6v6l4 2" />
                                            </svg>
                                            <span>Thời gian</span>
                                        </div>
                                        <div class="row g-3 align-items-end">
                                            <div class="col-12 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Ngày</label>
                                                <input type="date" id="eventStartDate" class="form-control register-control"
                                                    required>
                                            </div>
                                            <div class="col-6 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Giờ bắt
                                                    đầu</label>
                                                <input type="time" id="eventStartTime" class="form-control register-control"
                                                    required>
                                            </div>
                                            <div class="col-6 col-sm-4">
                                                <label class="form-label small fw-semibold text-dark mb-1">Giờ kết
                                                    thúc</label>
                                                <input type="time" id="eventEndTime" class="form-control register-control"
                                                    required>
                                            </div>
                                            <input type="hidden" id="eventEndDate">
                                        </div>
                                    </div>

                                    {{-- Lặp lại --}}
                                    <div class="repeat-section">
                                        <div class="repeat-header">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M17 1l4 4-4 4" />
                                                <path d="M3 11V9a4 4 0 0 1 4-4h14" />
                                                <path d="M7 23l-4-4 4-4" />
                                                <path d="M21 13v2a4 4 0 0 1-4 4H3" />
                                            </svg>
                                            <span>Lặp lại</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1">Tần suất</label>
                                                <select id="eventRepeatType" class="form-select register-control">
                                                    <option value="">Không lặp</option>
                                                    <option value="daily">Hàng ngày</option>
                                                    <option value="weekly">Hàng tuần</option>
                                                    <option value="monthly">Hàng tháng</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label small fw-semibold text-dark mb-1">Lặp đến
                                                    ngày</label>
                                                <input type="date" id="eventRepeatUntil"
                                                    class="form-control register-control">
                                                <div class="small text-muted mt-1">Để trống: chỉ tạo 1 lịch.</div>
                                            </div>

                                            {{-- Chọn thứ --}}
                                            <div class="col-12" id="weekdaySection" style="display:none;">
                                                <label class="form-label small fw-semibold text-dark mb-2">
                                                    Chọn ngày lặp lại trong tuần
                                                    <span id="weekSummary" class="text-muted fw-normal"></span>
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
                                                            <input type="checkbox" value="{{ $val }}" class="weekday-checkbox">
                                                            <span>{{ $label }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tệp đính kèm --}}
                                    <div>
                                        <label class="form-label small fw-semibold text-dark mb-1">Tệp đính kèm</label>
                                        <input type="file" id="eventFiles" name="files[]" multiple
                                            class="form-control register-control"
                                            accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                                        <div class="small text-muted mt-1">
                                            Hỗ trợ nhiều tệp, dung lượng mỗi tệp tối đa ~10MB.
                                        </div>
                                    </div>

                                    <input type="hidden" id="eventColor" value="#f59e0b">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn register-btn register-btn-ghost me-2" onclick="closeModal()">
                        Hủy
                    </button>
                    <button type="button" class="btn register-btn register-btn-success" onclick="saveEvent()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            class="me-2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                        Lưu
                    </button>
                </div>
            </div>
        </div>
    @endauth

    {{-- MODAL CHI TIẾT --}}
    <div id="detailModal" class="lab-modal">
        <div class="lab-modal-content detail-modal-card">
            <div class="modal-header border-0 pb-0">
                <h4 class="lab-modal-title">Chi tiết sự kiện</h4>
                <button type="button" class="btn btn-sm btn-light" onclick="closeDetailModal()">
                    <i class="ph-x"></i>
                </button>
            </div>

            <div class="modal-body pt-3">
                <div class="lab-detail-grid">
                    <div class="lab-detail-item">
                        <div class="lab-detail-label">Tiêu đề</div>
                        <div class="lab-detail-value" id="detailTitle"></div>
                    </div>

                    <div class="lab-detail-item">
                        <div class="lab-detail-label">Thời gian</div>
                        <div class="lab-detail-value" id="detailTime"></div>
                    </div>

                    <div class="lab-detail-item">
                        <div class="lab-detail-label">Phòng</div>
                        <div class="lab-detail-value" id="detailRoom"></div>
                    </div>

                    <div class="lab-detail-item" id="detailRegisteredForRow" style="display:none;">
                        <div class="lab-detail-label">Nhóm</div>
                        <div class="lab-detail-value" id="detailRegisteredFor"></div>
                    </div>

                    <div class="lab-detail-item" id="detailDescriptionRow" style="display:none;">
                        <div class="lab-detail-label">Mô tả</div>
                        <div class="lab-detail-value" id="detailDescription"></div>
                    </div>

                    <div class="lab-detail-item">
                        <div class="lab-detail-label">Loại</div>
                        <div class="lab-detail-value" id="detailCategory"></div>
                    </div>

                    <div class="lab-detail-item">
                        <div class="lab-detail-label">Trạng thái</div>
                        <div class="lab-detail-value d-flex align-items-center gap-2">
                            <span>
                                <i id="statusPendingIcon" class="fa-solid fa-clock"
                                    style="color:#f59e0b; display:none;"></i>
                                <i id="statusApprovedIcon" class="fa-solid fa-circle-check"
                                    style="color:#10b981; display:none;"></i>
                                <i id="statusCompletedIcon" class="fa-solid fa-check-double"
                                    style="color:#6366f1; display:none;"></i>
                                <i id="statusCancelledIcon" class="fa-solid fa-ban"
                                    style="color:#ef4444; display:none;"></i>
                            </span>
                            <span id="detailStatus"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                @auth
                    <button type="button" class="btn btn-danger" id="deleteEventBtn" onclick="deleteEvent()">
                        <i class="fa-regular fa-trash-can"></i>
                        <span>Xóa</span>
                    </button>
                    <button type="button" class="btn btn-primary" id="editEventBtn" onclick="editEvent()">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span>Sửa</span>
                    </button>
                @endauth
                <button type="button" class="btn btn-secondary" onclick="closeDetailModal()">Đóng</button>
            </div>
        </div>
    </div>

    {{-- MODAL XÁC NHẬN XÓA --}}
    <div id="confirmDeleteModal" class="lab-modal">
        <div class="lab-modal-content" style="max-width: 440px;">
            <div class="modal-header border-0 pb-0">
                <h4 class="lab-modal-title">Xác nhận xóa sự kiện</h4>
            </div>
            <div class="modal-body pt-2">
                <p>
                    Bạn có chắc chắn muốn xóa sự kiện này không?<br>
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmDelete()">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Xóa</button>
            </div>
        </div>
    </div>
    <div id="confirmConflictModal" class="lab-modal">
        <div class="lab-modal-content" style="max-width:520px;">
            <div class="modal-header border-0 pb-0">
                <h4 class="lab-modal-title">Lịch bị trùng</h4>
            </div>

            <div class="modal-body pt-2">
                <p class="mb-2">
                    Một số lịch bị trùng với <b>lịch đã duyệt</b>.
                    Bạn có muốn tiếp tục đăng ký <b>các lịch còn lại</b> không?
                </p>

                <ul id="conflictList" class="small text-muted ps-3 mb-0"></ul>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary" onclick="closeConflictModal()">Hủy</button>
                <button class="btn btn-danger" onclick="confirmContinue()">Vẫn đăng ký</button>
            </div>
        </div>
    </div>

    {{-- STYLES --}}
    <style>
        .register-page {
            --r-border: #e6eaf2;
            --r-text: #0f172a;
            --r-success: #16a34a;
            --r-primary: #2563eb;
        }

        .register-card {
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, .08);
        }

        .register-control {
            border: 1px solid var(--r-border) !important;
            border-radius: 12px !important;
            padding: 10px 12px !important;
        }

        .register-btn {
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            border: 1px solid transparent;
        }

        .register-btn-success {
            background: #f1f2f3;
            color: #26d320;
        }

        .register-btn-ghost {
            background: #f1f2f3;
            color: #334155;
            border: 1px solid var(--r-border);
        }

        .time-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid var(--r-border);
            border-radius: 16px;
            padding: 20px;
        }

        .time-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--r-text);
            margin-bottom: 16px;
            font-size: 15px;
        }

        .repeat-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #fbbf24;
            border-radius: 16px;
            padding: 20px;
        }

        .repeat-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .weekday-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
        }

        .weekday-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            background: #fff;
            border: 2px solid var(--r-border);
            border-radius: 12px;
            cursor: pointer;
            transition: all .2s ease;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
        }

        .weekday-btn input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .weekday-btn:hover {
            border-color: var(--r-primary);
            background: #f8fafc;
        }

        .weekday-btn:has(input:checked) {
            background: var(--r-primary);
            border-color: var(--r-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .25);
        }

        .sticky-col {
            position: sticky;
            top: 84px;
        }

        @media (max-width: 991.98px) {
            .sticky-col {
                position: static;
                top: auto;
            }
        }

        .lab-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, .35);
            z-index: 1050;
            padding: 16px;
        }

        .lab-modal.active {
            display: flex;
        }

        .lab-modal-content {
            width: 100%;
            max-width: 960px;
            background: #fff;
            border-radius: 18px;
            padding: 0;
        }

        .detail-modal-card {
            max-width: 560px;
        }
    </style>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Global variables for calendar
        window.LAB_USER = @json([
            'logged_in' => auth()->check() && auth()->user()->role_id !== 4,
            'is_admin' => auth()->check() && auth()->user()->role_id === 1,
            'user_id' => auth()->check() ? auth()->user()->id : null
        ]);

        window.LAB_ROOMS = @json($rooms->map(fn($r) => ['code' => $r->code, 'name' => $r->name])->values());
        window.LAB_GROUPS = @json($groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values());
    </script>
    <script src="{{ asset('assets/js/calendar.js') }}"></script>
</div>
