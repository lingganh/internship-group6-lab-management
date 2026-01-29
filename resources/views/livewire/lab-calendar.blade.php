<div>
    {{-- CSRF Token Meta --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- PAGE --}}
    <div class="lab-calendar-page">
        <div class="lab-calendar-shell">
            <div class="lab-layout">
                {{-- SIDEBAR --}}
                <aside class="lab-sidebar">
                    @auth
                        <button type="button" class="create-btn" wire:click="openCreateModal">
                            <i class="fa-solid fa-plus"></i>
                            <span>Tạo sự kiện</span>
                        </button>
                    @endauth

                    <div class="lab-mini-calendar" id="miniCalendar"></div>

                    <div class="lab-sidebar-section">
                        <div class="lab-sidebar-section-title">Trạng thái sự kiện</div>
                        <div class="lab-checklist">
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleStatusFilter('pending')">
                                <span class="lab-check-color status-pending"></span>
                                <span class="lab-check-label">Chờ duyệt</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleStatusFilter('approved')">
                                <span class="lab-check-color status-approved"></span>
                                <span class="lab-check-label">Đã duyệt</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleStatusFilter('completed')">
                                <span class="lab-check-color status-completed"></span>
                                <span class="lab-check-label">Đã hoàn thành</span>
                            </label>
                        </div>
                    </div>

                    <div class="lab-sidebar-section">
                        <div class="lab-sidebar-section-title">Loại sự kiện</div>
                        <div class="lab-checklist">
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleCategoryFilter('work')">
                                <span class="lab-check-label">Làm việc / nghiên cứu</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleCategoryFilter('seminar')">
                                <span class="lab-check-label">Hội thảo / seminar</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" 
                                    checked 
                                    wire:click="toggleCategoryFilter('other')">
                                <span class="lab-check-label">Khác</span>
                            </label>
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
        @if($showEventModal)
            <div class="lab-modal active">
                <div class="lab-modal-content register-card">
                    <div class="modal-header bg-white border-0 pb-0 px-4 pt-3">
                        <h4 class="fw-bold text-dark mb-0">
                            {{ $eventId ? 'Chỉnh sửa sự kiện' : 'Tạo lịch' }}
                        </h4>
                        <button type="button" class="btn btn-sm btn-light" wire:click="closeModal">
                            <i class="ph-x"></i>
                        </button>
                    </div>

                    <div class="modal-body pt-3 px-4 pb-4 register-page">
                        <form wire:submit.prevent="saveEvent">
                            <div class="row g-4">
                                {{-- CỘT TRÁI --}}
                                <div class="col-12 col-md-6">
                                    <div class="vstack gap-3">
                                        <div>
                                            <label class="form-label small fw-semibold text-dark mb-1">Tiêu đề *</label>
                                            <input type="text" 
                                                wire:model.defer="title" 
                                                class="form-control register-control @error('title') is-invalid @enderror" 
                                                placeholder="Nhập tiêu đề...">
                                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="form-label small fw-semibold text-dark mb-1">Hoạt động *</label>
                                            <select wire:model.defer="category" class="form-select register-control">
                                                <option value="work">Làm việc - Nghiên cứu</option>
                                                <option value="seminar">Hội thảo - Seminar</option>
                                                <option value="other">Khác</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="form-label small fw-semibold text-dark mb-1">Nhóm / lớp</label>
                                            <select wire:model.defer="registeredFor" class="form-select register-control">
                                                <option value="">Chọn nhóm / lớp...</option>
                                                @foreach($groups as $g)
                                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="form-label small fw-semibold text-dark mb-1">Mô tả</label>
                                            <textarea wire:model.defer="description" 
                                                rows="4" 
                                                class="form-control register-control" 
                                                placeholder="Nhập mô tả..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- CỘT PHẢI --}}
                                <div class="col-12 col-md-6">
                                    <div class="vstack gap-3 sticky-col">
                                        <div class="time-section">
                                            <div class="time-header">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M12 6v6l4 2"/>
                                                </svg>
                                                <span>Thời gian</span>
                                            </div>
                                            <div class="row g-3 align-items-end">
                                                <div class="col-12 col-sm-4">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Ngày *</label>
                                                    <input type="date" 
                                                        wire:model.defer="startDate" 
                                                        min="{{ now()->format('Y-m-d') }}"
                                                        class="form-control register-control @error('startDate') is-invalid @enderror">
                                                    @error('startDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Giờ bắt đầu *</label>
                                                    <input type="time" 
                                                        wire:model.defer="startTime" 
                                                        class="form-control register-control @error('startTime') is-invalid @enderror">
                                                    @error('startTime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Giờ kết thúc *</label>
                                                    <input type="time" 
                                                        wire:model.defer="endTime" 
                                                        class="form-control register-control @error('endTime') is-invalid @enderror">
                                                    @error('endTime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if(!$eventId)
                                            <div class="repeat-section">
                                                <div class="repeat-header">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                                        <select wire:model="repeatType" class="form-select register-control">
                                                            <option value="">Không lặp</option>
                                                            <option value="daily">Hàng ngày</option>
                                                            <option value="weekly">Hàng tuần</option>
                                                            <option value="monthly">Hàng tháng</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <label class="form-label small fw-semibold text-dark mb-1">Lặp đến ngày</label>
                                                        <input type="date" 
                                                            wire:model.defer="repeatUntil" 
                                                            min="{{ now()->format('Y-m-d') }}"
                                                            class="form-control register-control">
                                                        <div class="small text-muted mt-1">Để trống: chỉ tạo 1 lịch.</div>
                                                    </div>

                                                    @if($repeatType === 'weekly')
                                                        <div class="col-12">
                                                            <label class="form-label small fw-semibold text-dark mb-2">
                                                                Chọn ngày lặp lại trong tuần
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
                                                                            wire:model.defer="repeatDays">
                                                                        <span>{{ $label }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <div>
                                            <label class="form-label small fw-semibold text-dark mb-1">Tệp đính kèm</label>
                                            <input type="file" 
                                                wire:model="files" 
                                                multiple 
                                                class="form-control register-control" 
                                                accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                                            <div class="small text-muted mt-1">
                                                Hỗ trợ nhiều tệp, dung lượng mỗi tệp tối đa ~10MB.
                                            </div>
                                            @error('files.*') <div class="text-danger small">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn register-btn register-btn-ghost me-2" wire:click="closeModal">
                            Hủy
                        </button>
                        <button type="button" class="btn register-btn register-btn-success" wire:click="saveEvent">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            Lưu
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- MODAL CHI TIẾT --}}
    @if($showDetailModal && $detailEvent)
        <div class="lab-modal active">
            <div class="lab-modal-content detail-modal-card">
                <div class="modal-header border-0 pb-0">
                    <h4 class="lab-modal-title">Chi tiết sự kiện</h4>
                    <button type="button" class="btn btn-sm btn-light" wire:click="closeModal">
                        <i class="ph-x"></i>
                    </button>
                </div>

                <div class="modal-body pt-3">
                    <div class="lab-detail-grid">
                        <div class="lab-detail-item">
                            <div class="lab-detail-label">Tiêu đề</div>
                            <div class="lab-detail-value">{{ $detailEvent['title'] }}</div>
                        </div>

                        <div class="lab-detail-item">
                            <div class="lab-detail-label">Thời gian</div>
                            <div class="lab-detail-value">
                                {{ \Carbon\Carbon::parse($detailEvent['start'])->format('d/m/Y H:i') }} - 
                                {{ \Carbon\Carbon::parse($detailEvent['end'])->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="lab-detail-item">
                            <div class="lab-detail-label">Phòng</div>
                            <div class="lab-detail-value">{{ $detailEvent['roomName'] ?? 'N/A' }}</div>
                        </div>

                        @if($detailEvent['registeredForName'])
                            <div class="lab-detail-item">
                                <div class="lab-detail-label">Nhóm</div>
                                <div class="lab-detail-value">{{ $detailEvent['registeredForName'] }}</div>
                            </div>
                        @endif

                        @if($detailEvent['description'])
                            <div class="lab-detail-item">
                                <div class="lab-detail-label">Mô tả</div>
                                <div class="lab-detail-value">{{ $detailEvent['description'] }}</div>
                            </div>
                        @endif

                        <div class="lab-detail-item">
                            <div class="lab-detail-label">Loại</div>
                            <div class="lab-detail-value">
                                @php
                                    $categoryNames = [
                                        'work' => 'Làm việc - nghiên cứu',
                                        'seminar' => 'Hội thảo - Seminar',
                                        'other' => 'Khác'
                                    ];
                                @endphp
                                {{ $categoryNames[$detailEvent['category']] ?? $detailEvent['category'] }}
                            </div>
                        </div>

                        <div class="lab-detail-item">
                            <div class="lab-detail-label">Trạng thái</div>
                            <div class="lab-detail-value d-flex align-items-center gap-2">
                                <span>
                                    @if($detailEvent['status'] === 'pending')
                                        <i class="fa-solid fa-clock" style="color:#f59e0b;"></i>
                                    @elseif($detailEvent['status'] === 'approved')
                                        <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                                    @elseif($detailEvent['status'] === 'completed')
                                        <i class="fa-solid fa-check-double" style="color:#6366f1;"></i>
                                    @elseif($detailEvent['status'] === 'cancelled')
                                        <i class="fa-solid fa-ban" style="color:#ef4444;"></i>
                                    @endif
                                </span>
                                <span>
                                    @php
                                        $statusLabels = [
                                            'pending' => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'completed' => 'Đã hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                    @endphp
                                    {{ $statusLabels[$detailEvent['status']] ?? $detailEvent['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    @auth
                        @if($detailEvent['canEdit'])
                            <button type="button" class="btn btn-danger" wire:click="deleteEvent">
                                <i class="fa-regular fa-trash-can"></i>
                                <span>Xóa</span>
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="openEditModal({{ $detailEvent['id'] }})">
                                <i class="fa-regular fa-pen-to-square"></i>
                                <span>Sửa</span>
                            </button>
                        @endif
                    @endauth
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Đóng</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL XÁC NHẬN XÓA --}}
    @if($showDeleteModal)
        <div class="lab-modal active">
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
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                    <button type="button" class="btn btn-danger" wire:click="confirmDelete">Xóa</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL XUNG ĐỘT --}}
    @if($showConflictModal)
        <div class="lab-modal active">
            <div class="lab-modal-content" style="max-width:520px;">
                <div class="modal-header border-0 pb-0">
                    <h4 class="lab-modal-title">Lịch bị trùng</h4>
                </div>

                <div class="modal-body pt-2">
                    <p class="mb-2">
                        Một số lịch bị trùng với <b>lịch đã duyệt</b>.
                        Bạn có muốn tiếp tục đăng ký <b>các lịch còn lại</b> không?
                    </p>

                    <ul class="small text-muted ps-3 mb-0">
                        @foreach($conflicts as $c)
                            <li class="mb-2">
                                <b>{{ $c['requested_start'] }} → {{ $c['requested_end'] }}</b><br>
                                <span class="text-danger">
                                    Trùng với: {{ $c['conflict_with']['title'] }}
                                    ({{ $c['conflict_with']['start'] }} → {{ $c['conflict_with']['end'] }})
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                    <button class="btn btn-danger" wire:click="confirmContinue">Vẫn đăng ký</button>
                </div>
            </div>
        </div>
    @endif

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const miniCalendarEl = document.getElementById('miniCalendar');
            
            let calendar, miniCalendar;

            // Initialize mini calendar
            if (miniCalendarEl) {
                miniCalendar = new FullCalendar.Calendar(miniCalendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'vi',
                    firstDay: 0,
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    buttonText: {
                        prev: '‹',
                        next: '›'
                    },
                    height: 'auto',
                    contentHeight: 'auto',
                    expandRows: true,
                    fixedWeekCount: false,
                    showNonCurrentDates: true,
                    selectable: false,
                    dayMaxEvents: false,
                    navLinks: false,
                    dateClick: function (info) {
                        if (calendar) calendar.gotoDate(info.date);
                    }
                });
                miniCalendar.render();
            }

            // Initialize main calendar
            if (calendarEl) {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    locale: 'vi',
                    firstDay: 1,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Hôm nay',
                        month: 'Tháng',
                        week: 'Tuần',
                        day: 'Ngày'
                    },
                    slotMinTime: '07:00:00',
                    slotMaxTime: '19:00:00',
                    allDaySlot: false,
                    nowIndicator: true,
                    editable: false,
                    selectable: @json(auth()->check()),
                    dayMaxEvents: true,
                    weekends: true,
                    height: 'auto',
                    events: @json($events),
                    eventClick: function(info) {
                        @this.call('showEventDetails', info.event.id);
                    },
                    select: function(info) {
                        const now = new Date();
                        if (info.start < now) {
                            alert('Không thể đăng ký sự kiện trong quá khứ');
                            calendar.unselect();
                            return;
                        }
                        @this.call('openCreateModal', info.start.toISOString(), info.end.toISOString());
                    },
                });
                
                calendar.render();

                // Livewire hook to refresh calendar
                Livewire.hook('message.processed', (message, component) => {
                    if (calendar) {
                        calendar.refetchEvents();
                    }
                });
            }
        });
    </script>
</div>