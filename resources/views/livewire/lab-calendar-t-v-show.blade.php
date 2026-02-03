<div>
    <meta http-equiv="refresh" content="600">
    {{-- CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f9fafb;
        }

        .tv-calendar-page {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .tv-layout {
            display: flex;
            flex: 1;
            min-height: 0;
            position: relative;
        }

        /* ============= SIDEBAR ============= */
        .tv-sidebar {
            width: 320px;
            min-width: 320px;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: transform 0.3s ease, margin-left 0.3s ease;
            z-index: 20;
        }

        .tv-sidebar.hidden {
            transform: translateX(-100%);
            margin-left: -320px;
        }

        .tv-sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .tv-header-title {
            margin-bottom: 1rem;
        }

        .tv-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1.4;
        }

        .tv-sidebar-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .tv-clock {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        .tv-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .tv-sidebar-content::-webkit-scrollbar {
            width: 6px;
        }

        .tv-sidebar-content::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .tv-sidebar-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .tv-filter-section {
            margin-bottom: 2rem;
        }

        .tv-filter-section:last-child {
            margin-bottom: 0;
        }

        .tv-filter-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .tv-filter-items {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .tv-filter-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
            border: 1px solid transparent;
        }

        .tv-filter-item:hover {
            background-color: #f3f4f6;
            border-color: #e5e7eb;
        }

        /* Custom Checkbox */
        .tv-filter-checkbox {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .custom-checkbox {
            width: 24px;
            height: 24px;
            border-radius: 0.5rem;
            border: 2px solid #d1d5db;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            position: relative;
        }

        .custom-checkbox::after {
            content: '';
            position: absolute;
            display: none;
            left: 7px;
            top: 3px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .tv-filter-checkbox:checked ~ .custom-checkbox::after {
            display: block;
        }

        .tv-filter-checkbox:checked ~ .custom-checkbox {
            border-color: transparent;
        }

        /* Status checkbox colors */
        .tv-filter-item.status-pending .tv-filter-checkbox:checked ~ .custom-checkbox {
            background: #f59e0b;
        }

        .tv-filter-item.status-approved .tv-filter-checkbox:checked ~ .custom-checkbox {
            background: #10b981;
        }

        .tv-filter-item.status-completed .tv-filter-checkbox:checked ~ .custom-checkbox {
            background: #6366f1;
        }

        /* Category checkbox - đồng màu xanh dương */
        .tv-filter-item.category-filter .tv-filter-checkbox:checked ~ .custom-checkbox {
            background: #3b82f6;
        }

        .tv-filter-label {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
            flex: 1;
        }

        .tv-filter-icon {
            font-size: 1rem;
            color: #6b7280;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ============= TOGGLE BUTTON ============= */
        .tv-toggle-btn {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            z-index: 25;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .tv-toggle-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .tv-toggle-btn:active {
            transform: scale(0.95);
        }

        .tv-toggle-btn i {
            font-size: 1.25rem;
            color: #374151;
            transition: transform 0.3s;
        }

        .tv-toggle-btn.sidebar-hidden i {
            transform: rotate(180deg);
        }

        /* ============= MAIN CONTENT ============= */
        .tv-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
        }

        .tv-calendar-container {
            flex: 1;
            padding: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #calendar {
            flex: 1;
            min-height: 0;
        }

        /* ============= CALENDAR STYLES ============= */
        .fc {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            height: 100% !important;
        }

        .fc .fc-view-harness {
            height: 100% !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e5e7eb;
        }

        .fc-col-header-cell {
            background: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            color: #6b7280;
            padding: 0.75rem 0.25rem;
        }

        .fc-daygrid-day-number {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 8px;
            color: #374151;
        }

        .fc-day-today {
            background: #eff6ff !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fc-event {
            border: none !important;
            padding: 4px 8px;
            margin: 1px 2px;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .fc-event:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fc-event-title {
            font-weight: 600;
        }

        .fc-event-time {
            font-weight: 500;
            opacity: 0.95;
        }

        .event-current {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3) !important;
        }

        .event-past {
            opacity: 0.4 !important;
            filter: grayscale(30%);
        }

        .event-past:hover {
            opacity: 0.6 !important;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.85;
            }
        }

        .fc-timegrid-now-indicator-line {
            border-color: #ea4335 !important;
            border-width: 2px 0 0 !important;
            z-index: 5;
        }

        .fc .fc-timegrid-slot {
            height: 2.5em;
        }

        .fc-timegrid-now-indicator-line::before {
            content: "";
            position: absolute;
            left: -6px;
            top: -6px;
            width: 12px;
            height: 12px;
            background-color: #ea4335;
            border-radius: 50%;
            z-index: 6;
        }

        .fc-scroller {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        .fc-scroller::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .fc-scroller::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .fc-scroller::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        /* ============= MODAL ============= */
        .tv-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .tv-modal.active {
            display: flex;
        }

        .tv-modal-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .modal-close {
            background: #f3f4f6;
            border: none;
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #e5e7eb;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .detail-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-icon {
            color: #9ca3af;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #111827;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>

    {{-- PAGE --}}
    <div class="tv-calendar-page">
        {{-- TOGGLE BUTTON --}}
        <button type="button" class="tv-toggle-btn" id="toggleBtn" onclick="toggleSidebar()">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="tv-layout">
            {{-- SIDEBAR --}}
            <aside class="tv-sidebar" id="tvSidebar">
                <div class="tv-sidebar-header">
                    <div class="tv-header-title">
                        <h3 class="tv-title">📅 LAB Phát triển phần mềm và hệ thống thông minh</h3>
                    </div>
                    <h3 class="tv-sidebar-title">Bộ lọc</h3>
                    <div class="tv-clock" id="sidebar-clock"></div>
                </div>

                <div class="tv-sidebar-content">
                    {{-- Filter by Status --}}
                    <div class="tv-filter-section">
                        <div class="tv-filter-title">Trạng thái</div>
                        <div class="tv-filter-items">
                            @foreach($statuses as $status)
                                <label class="tv-filter-item status-{{ $status['code'] }}">
                                    <input type="checkbox" 
                                           class="tv-filter-checkbox status-filter" 
                                           data-status="{{ $status['code'] }}" 
                                           checked 
                                           onchange="applyFilters()">
                                    <span class="custom-checkbox"></span>
                                    <span class="tv-filter-label">{{ $status['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Filter by Category --}}
                    <div class="tv-filter-section">
                        <div class="tv-filter-title">Loại sự kiện</div>
                        <div class="tv-filter-items">
                            @foreach($categories as $category)
                                <label class="tv-filter-item category-filter">
                                    <input type="checkbox" 
                                           class="tv-filter-checkbox category-filter-input" 
                                           data-category="{{ $category['code'] }}" 
                                           checked 
                                           onchange="applyFilters()">
                                    <span class="custom-checkbox"></span>
                                    <i class="fa-solid fa-{{ $category['icon'] }} tv-filter-icon"></i>
                                    <span class="tv-filter-label">{{ $category['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            {{-- MAIN CONTENT --}}
            <div class="tv-main">
                <div class="tv-calendar-container">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Chi tiết --}}
    <div id="detailModal" class="tv-modal">
        <div class="tv-modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Chi tiết sự kiện</h3>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Tiêu đề</div>
                        <div class="detail-value" id="modal-title"></div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Thời gian</div>
                        <div class="detail-value" id="modal-time"></div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Loại sự kiện</div>
                        <div class="detail-value" id="modal-category"></div>
                    </div>
                </div>

                <div class="detail-item" id="modal-description-wrapper" style="display: none;">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Mô tả</div>
                        <div class="detail-value" id="modal-description"></div>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Trạng thái</div>
                        <div id="modal-status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    setTimeout(function() {
        window.location.href = window.location.href;
    }, 600000);

    var calendar = null;
    var events = @json($events);
    var hiddenStatuses = [];
    var hiddenCategories = [];
    var sidebarVisible = true;

    // Toggle sidebar
    function toggleSidebar() {
        var sidebar = document.getElementById('tvSidebar');
        var toggleBtn = document.getElementById('toggleBtn');
        
        sidebarVisible = !sidebarVisible;
        
        if (sidebarVisible) {
            sidebar.classList.remove('hidden');
            toggleBtn.classList.remove('sidebar-hidden');
        } else {
            sidebar.classList.add('hidden');
            toggleBtn.classList.add('sidebar-hidden');
        }
    }

    // Init clock
    function updateClock() {
        var now = new Date();
        var str = now.toLocaleString('vi-VN', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        var clockEl = document.getElementById('sidebar-clock');
        if (clockEl) {
            clockEl.textContent = str;
        }
    }

    updateClock();
    setInterval(updateClock, 1000);

    // Check if event is past
    function isEventPast(eventEnd) {
        var now = new Date();
        var end = new Date(eventEnd);
        return end < now;
    }

    // Apply filters - NHANH HƠN
    function applyFilters() {
        hiddenStatuses = [];
        hiddenCategories = [];
        
        var statusCheckboxes = document.querySelectorAll('.status-filter');
        for (var i = 0; i < statusCheckboxes.length; i++) {
            if (!statusCheckboxes[i].checked) {
                hiddenStatuses.push(statusCheckboxes[i].getAttribute('data-status'));
            }
        }
        
        var categoryCheckboxes = document.querySelectorAll('.category-filter-input');
        for (var j = 0; j < categoryCheckboxes.length; j++) {
            if (!categoryCheckboxes[j].checked) {
                hiddenCategories.push(categoryCheckboxes[j].getAttribute('data-category'));
            }
        }
        
        // CHỈ GỌI refetchEvents() THAY VÌ XÓA VÀ THÊM LẠI
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    // Init calendar - DÙNG FUNCTION CALLBACK
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

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
            height: '100%',
            contentHeight: '100%',
            expandRows: true,
             events: function(info, successCallback, failureCallback) {
                var filteredEvents = events.filter(function(e) {
                    var statusHidden = hiddenStatuses.indexOf(e.status) !== -1;
                    var categoryHidden = hiddenCategories.indexOf(e.category) !== -1;
                    return !statusHidden && !categoryHidden;
                }).map(function(e) {
                    var bgColor = e.color || '#3b82f6';
                    var classes = [];
                    
                    if (e.is_current) {
                        classes.push('event-current');
                    } else if (isEventPast(e.end)) {
                        classes.push('event-past');
                    }

                    return {
                        id: e.id,
                        title: e.title,
                        start: e.start,
                        end: e.end,
                        backgroundColor: bgColor,
                        borderColor: bgColor,
                        textColor: '#ffffff',
                        classNames: classes,
                        extendedProps: {
                            category: e.category,
                            category_icon: e.category_icon,
                            category_name: e.category_name,
                            status_name: e.status_name,
                            status: e.status,
                            lab_code: e.lab_code,
                            lab_name: e.lab_name,
                            description: e.description,
                            registered_for: e.registered_for,
                            is_current: e.is_current
                        }
                    };
                });
                
                successCallback(filteredEvents);
            },
            eventClick: function(info) {
                showEventDetails(info.event);
            }
        });

        calendar.render();
    });

    // Show event details
    function showEventDetails(event) {
        var props = event.extendedProps;

        document.getElementById('modal-title').textContent = event.title;

        var startDate = new Date(event.start);
        var endDate = new Date(event.end);
        var timeStr = formatDateTime(startDate) + ' - ' + formatDateTime(endDate);
        document.getElementById('modal-time').textContent = timeStr;

        var categoryIcon = props.category_icon ? '<i class="fa-solid fa-' + props.category_icon + '"></i> ' : '';
        var categoryText = props.category_name || props.category;
        document.getElementById('modal-category').innerHTML = categoryIcon + categoryText;

        var descWrapper = document.getElementById('modal-description-wrapper');
        var descSpan = document.getElementById('modal-description');

        if (props.description) {
            descSpan.textContent = props.description;
            descWrapper.style.display = 'flex';
        } else {
            descWrapper.style.display = 'none';
        }

        var statusText = props.status_name || props.status;
        var statusBadge = '<span class="status-badge" style="background-color: ' + event.backgroundColor + '; color: white;">' + statusText + '</span>';
        document.getElementById('modal-status').innerHTML = statusBadge;

        document.getElementById('detailModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.remove('active');
    }

    function formatDateTime(date) {
        return date.toLocaleString('vi-VN', {
            weekday: 'short',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>
</div>