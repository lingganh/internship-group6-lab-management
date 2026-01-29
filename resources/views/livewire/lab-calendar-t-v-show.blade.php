<div wire:poll.300s="loadData">
    {{-- CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

    <style>
        .tv-calendar-page {
            min-height: 100vh;
            background: #f9fafb;
        }

        .tv-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .tv-header-content {
            max-width: 1600px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tv-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .tv-clock {
            font-size: 1.125rem;
            color: #6b7280;
            font-weight: 500;
        }

        .tv-legend {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .tv-calendar-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* FullCalendar custom styles */
        .fc {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e5e7eb;
        }

        .fc-col-header-cell {
            background: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6b7280;
            padding: 1rem 0;
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
            padding: 6px 10px;
            margin: 2px 4px;
            border-radius: 6px;
            font-size: 0.875rem;
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

        /* Event is currently happening - pulse animation */
        .event-current {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2) !important;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.85;
            }
        }

        /* Category colors */
        .event-work {
            background: #bc307b;
            color: white;
        }

        .event-seminar {
            background: #c4b517;
            color: white;
        }

        .event-other {
            background: #4d6d41;
            color: white;
        }

        /* Status colors */
        .event-approved {
            background: #10b981;
            color: white;
        }

        .event-completed {
            background: #6366f1;
            color: white;
        }

        /* Modal styles */
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

        .fc-timegrid-now-indicator-line {
            border-color: #ea4335 !important;
            /* Màu đỏ của Google */
            border-width: 2px 0 0 !important;
            z-index: 5;
        }

        .fc .fc-timegrid-slot {
            height: 3.0em;
            /* chỉnh 2.6em ~ 3.6em tùy thích */
        }


        /* Tạo hình tròn ở đầu đường kẻ (Marker) */
        .fc-timegrid-now-indicator-line::before {
            content: "";
            position: absolute;
            left: -6px;
            /* Điều chỉnh để hình tròn nằm đè lên trục thời gian */
            top: -6px;
            /* Căn giữa hình tròn với đường kẻ 2px */
            width: 12px;
            height: 12px;
            background-color: #ea4335;
            border-radius: 50%;
            z-index: 6;
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

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-completed {
            background: #e0e7ff;
            color: #3730a3;
        }
    </style>

    {{-- PAGE --}}
    <div class="tv-calendar-page">
        {{-- Header --}}
        <div class="tv-header">
            <div class="tv-header-content">
                <div>
                    <h3 class="tv-title">📅 LAB Phát triển phần mềm và hệ thống thông minh</h3>
                    <div class="tv-clock" id="current-time"></div>
                </div>

                <div class="tv-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #10b981;"></span>
                        <span>Đã duyệt</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #6366f1;"></span>
                        <span>Hoàn thành</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background: #22c55e; animation: pulse 2s infinite;"></span>
                        <span>Đang diễn ra</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="tv-calendar-container">
            <div id="calendar" wire:ignore></div>
        </div>

    </div>

    {{-- Modal Chi tiết --}}
    <div id="detailModal" class="tv-modal">
        <div class="tv-modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Chi tiết sự kiện</h3>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Phòng lab</div>
                        <div class="detail-value" id="modal-lab"></div>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
     
         document.addEventListener('livewire:initialized', () => {
              setInterval(() => {
                @this.call('loadData')
                }, 100000)
        })
        let calendar = null;
        const events = @json($events);

        // Init clock
        function updateClock() {
            const now = new Date();
            const str = now.toLocaleString('vi-VN', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = str;
        }

        updateClock();
        setInterval(updateClock, 1000);

        // Init calendar
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

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
                height: 'auto',
                events: events.map(e => {
                    const statusColors = {
                        'approved': '#10b981',
                        'completed': '#6366f1'
                    };

                    const categoryColors = {
                        'work': '#bc307b',
                        'seminar': '#c4b517',
                        'other': '#4d6d41'
                    };

                    const bgColor = statusColors[e.status] || categoryColors[e.category] || '#3b82f6';

                    return {
                        id: e.id,
                        title: e.title,
                        start: e.start,
                        end: e.end,
                        backgroundColor: bgColor,
                        borderColor: bgColor,
                        textColor: '#ffffff',
                        classNames: e.is_current ? ['event-current'] : [],
                        extendedProps: {
                            category: e.category,
                            status: e.status,
                            lab_code: e.lab_code,
                            lab_name: e.lab_name,
                            description: e.description,
                            registered_for: e.registered_for,
                            is_current: e.is_current
                        }
                    };
                }),
                eventClick: function (info) {
                    showEventDetails(info.event);
                }
            });

            calendar.render();
        });

        // Show event details
        function showEventDetails(event) {
            const props = event.extendedProps;

            document.getElementById('modal-title').textContent = event.title;

            const startDate = new Date(event.start);
            const endDate = new Date(event.end);
            const timeStr = `${formatDateTime(startDate)} - ${formatTime(endDate)}`;
            document.getElementById('modal-time').textContent = timeStr;

            document.getElementById('modal-lab').textContent = props.lab_name || props.lab_code;
            const categoryIcons = {
                'work': '💼',
                'seminar': '🎓',
                'other': '📌'
            };
            const categoryLabels = {
                'work': 'Làm việc - Nghiên cứu',
                'seminar': 'Hội thảo - Seminar',
                'other': 'Khác'
            };
            const catIcon = categoryIcons[props.category] || '📌';
            const catLabel = categoryLabels[props.category] || 'Khác';
            document.getElementById('modal-category').textContent = `${catIcon} ${catLabel}`;

            const descWrapper = document.getElementById('modal-description-wrapper');
            if (props.description) {
                document.getElementById('modal-description').textContent = props.description;
                descWrapper.style.display = 'flex';
            } else {
                descWrapper.style.display = 'none';
            }

            const statusText = props.status === 'approved' ? 'Đã duyệt' : 'Hoàn thành';
            const statusClass = `status-${props.status}`;
            const statusIcon = props.is_current ? '' : (props.status === 'approved' ? ' ' : ' ');

            document.getElementById('modal-status').innerHTML = `
                <span class="status-badge ${statusClass}">
                    <span>${statusIcon}</span>
                    <span>${statusText}</span>
                </span>
            `;

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

        function formatTime(date) {
            return date.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Livewire hook để reload calendar khi data thay đổi
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                if (calendar && component.fingerprint.name === 'lab-calendar-t-v-show') {
                    const newEvents = @this.events;
                    calendar.removeAllEvents();
                    calendar.addEventSource(newEvents.map(e => {
                        const statusColors = {
                            'approved': '#10b981',
                            'completed': '#6366f1'
                        };
                        const bgColor = statusColors[e.status] || '#3b82f6';

                        return {
                            id: e.id,
                            title: e.title,
                            start: e.start,
                            end: e.end,
                            backgroundColor: bgColor,
                            borderColor: bgColor,
                            textColor: '#ffffff',
                            classNames: e.is_current ? ['event-current'] : [],
                            extendedProps: e
                        };
                    }));
                }
            });
        });
    </script>
</div>