<div>
    <style>
        :root {
            --lab-primary: #1a73e8;
            --lab-primary-soft: #e8f0fe;
            --lab-bg: #f5f7fb;
            --lab-border: #dadce0;
            --lab-text-main: #202124;
            --lab-text-muted: #5f6368;
            --lab-danger: #d93025;
            --lab-success: #28a745;
            --lab-warning: #ffc107;
            --lab-radius-lg: 16px;
        }

        body {
            background: var(--lab-bg);
        }

        .lab-calendar-page {
            min-height: calc(100vh - 65px);
            padding: 20px 24px;
        }

        .lab-calendar-shell {
            width: 100%;
            margin: 0;
        }

        /* ===== LAYOUT: SIDEBAR + MAIN ===== */
        .lab-layout {
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }

        .lab-sidebar {
            width: 260px;
            border-radius: 20px;
            background: #ffffff;
            padding: 16px 14px 18px;
            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06),
                0 0 0 1px rgba(221, 225, 230, 0.9);
        }

        .lab-sidebar-section {
            margin-bottom: 18px;
        }

        .lab-sidebar-section-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--lab-text-main);
            margin-bottom: 8px;
        }

        .create-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--lab-primary);
            color: white;
            border: none;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(26, 115, 232, 0.35);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
            width: 100%;
            margin-bottom: 16px;
        }

        .create-btn i {
            font-size: 14px;
        }

        .create-btn:hover {
            background: #1765cc;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(26, 115, 232, 0.4);
        }

        .create-btn:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(26, 115, 232, 0.35);
        }

        /* ===== SIDEBAR CHECKBOX LIST ===== */
        .lab-checklist {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 13px;
        }

        .lab-check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 6px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .lab-check-item:hover {
            background: #f3f4f6;
        }

        .lab-check-item input[type="checkbox"] {
            margin: 0;
            width: 14px;
            height: 14px;
        }

        .lab-check-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .lab-check-color.status-pending {
            background: #facc15;
        }

        .lab-check-color.status-approved {
            background: #22c55e;
        }

        .lab-check-color.cat-work {
            background: #1a73e8;
        }

        .lab-check-color.cat-seminar {
            background: #f97316;
        }

        .lab-check-color.cat-other {
            background: #6366f1;
        }

        .lab-check-label {
            color: #374151;
        }

        .lab-calendar-main {
            flex: 1;
        }


        .lab-calendar-card {
            background: #ffffff;
            border-radius: var(--lab-radius-lg);
            padding: 16px 18px 18px;
            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06),
                0 0 0 1px rgba(221, 225, 230, 0.9);
        }


        #calendar {
            background: white;
            border-radius: 12px;
            border: 1px solid #e0e3e7;
            padding: 8px;
        }

        .fc {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 13px;
            color: var(--lab-text-main);
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 10px;
        }

        .fc .fc-toolbar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--lab-text-main);
        }

        .fc .fc-button {
            background: #fff;
            color: #3c4043;
            border: 1px solid var(--lab-border);
            padding: 6px 12px;
            text-transform: none;
            font-weight: 500;
            border-radius: 999px;
            box-shadow: none;
        }

        .fc .fc-button:hover {
            background: #f8f9fa;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background: #e8f0fe;
            color: var(--lab-primary);
            border-color: var(--lab-primary);
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #dde3ea;
        }

        .fc .fc-col-header-cell-cushion {
            padding: 6px 4px;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .fc .fc-daygrid-day-number {
            font-size: 12px;
            padding-top: 4px;
            padding-right: 6px;
            color: #4b5563;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background: #fff8e1;
        }

        .fc-event {
            border-radius: 8px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            padding: 2px 4px;
            font-size: 11px;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.25);
            background-clip: padding-box;
        }


        .fc-timegrid-event,
        .fc-timegrid-event-harness,
        .fc-timegrid-event .fc-event-main,
        .fc-timegrid-event .fc-event-main-frame {
            overflow: hidden;
        }

        .fc-event-main-custom {
            font-size: 11px;
            line-height: 1.35;
            color: #fff;
        }

        .fc-event-main-custom .fc-event-time {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .fc-event-main-custom .fc-event-title {
            font-weight: 500;
        }

        .fc-event-main-custom .fc-event-status {
            font-size: 10px;
            margin-top: 2px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            opacity: 0.95;
            padding: 1px 6px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.2);
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* VIỀN NÉT ĐỨT CHO SỰ KIỆN CHỜ DUYỆT */
        .fc-event.is-pending {
            border-style: dashed;
            border-width: 2px;
            border-color: #fbbf24;
            background-color: rgba(251, 191, 36, 0.12);
        }

        /* ===== MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at top, rgba(26, 115, 232, 0.10), transparent 45%),
                rgba(15, 23, 42, 0.45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
            padding: 12px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: var(--lab-radius-lg);
            width: 100%;
            max-width: 720px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.28),
                0 0 0 1px rgba(148, 163, 184, 0.4);
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 18px 22px 14px;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .modal-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--lab-text-main);
            margin: 0;
        }

        .modal-body {
            padding: 18px 22px 8px;
            overflow: auto;
        }

        .form-layout-grid {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2.5fr);
            gap: 16px 20px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--lab-text-muted);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid var(--lab-border);
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: #fbfcfe;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 70px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--lab-primary);
            background: #fff;
            box-shadow: 0 0 0 1px rgba(26, 115, 232, 0.25);
        }

        .time-inputs {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 10px;
        }

        .modal-footer {
            padding: 12px 20px 14px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid #edf0f4;
            background: #fafbff;
        }

        .btn {
            padding: 8px 18px;
            border: none;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: var(--lab-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(26, 115, 232, 0.28);
        }

        .btn-primary:hover {
            background: #1765cc;
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
        }

        .btn-secondary {
            background: #fff;
            color: var(--lab-text-muted);
            border: 1px solid var(--lab-border);
        }

        .btn-secondary:hover {
            background: #f7f8fa;
        }

        .btn-danger {
            background: var(--lab-danger);
            color: #fff;
            box-shadow: 0 2px 8px rgba(217, 48, 37, 0.3);
        }

        .btn-danger:hover {
            background: #c5221f;
        }

        .event-details {
            padding: 18px 22px 16px;
        }

        .event-details h2 {
            font-size: 20px;
            margin-bottom: 14px;
            color: var(--lab-text-main);
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: var(--lab-text-main);
        }

        .detail-icon {
            width: 20px;
            text-align: center;
            color: var(--lab-text-muted);
            margin-top: 1px;
        }

        #detailDescription {
            white-space: pre-line;
        }

        #confirmDeleteModal .modal-content {
            max-width: 380px;
        }

        #confirmDeleteModal .modal-body p {
            line-height: 1.5;
            font-size: 13px;
        }

        .lab-mini-calendar {
            width: 100%;
            background: transparent;
            padding: 4px 4px 10px;
            font-family: 'Google Sans', Roboto, Arial, sans-serif;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .lab-mini-calendar .fc-theme-standard td,
        .lab-mini-calendar .fc-theme-standard th,
        .lab-mini-calendar .fc-theme-standard .fc-scrollgrid {
            border: none !important;
        }

        .lab-mini-calendar .fc-toolbar {
            margin-bottom: 8px !important;
            padding: 0 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lab-mini-calendar .fc-toolbar-title {
            font-size: 14px !important;
            font-weight: 500;
            color: #3c4043;
            letter-spacing: 0.25px;
        }

        .lab-mini-calendar .fc-button-group {
            gap: 0;
        }

        .lab-mini-calendar .fc-button {
            background: transparent !important;
            border: none !important;
            color: #5f6368 !important;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .lab-mini-calendar .fc-button:hover {
            background-color: #f1f3f4 !important;
            color: #3c4043 !important;
        }

        .lab-mini-calendar .fc-col-header {
            background: transparent;
        }

        .lab-mini-calendar th {
            text-align: center;
            padding: 4px 0 !important;
            font-weight: 500;
        }

        .lab-mini-calendar .fc-col-header-cell-cushion {
            font-size: 11px;
            color: #70757a;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 0;
            text-decoration: none;
        }

        .lab-mini-calendar .fc-daygrid-body {
            border: none !important;
        }

        .lab-mini-calendar .fc-daygrid-day {
            padding: 0 !important;
        }

        .lab-mini-calendar .fc-daygrid-day-frame {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 2px;
        }

        .lab-mini-calendar .fc-daygrid-day-top {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .lab-mini-calendar .fc-daygrid-day-number {
            width: 26px;
            height: 26px;
            line-height: 26px;
            text-align: center;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 400;
            color: #3c4043;
            padding: 0 !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.12s ease, color 0.12s ease;
        }

        .lab-mini-calendar .fc-day-other .fc-daygrid-day-number {
            color: #dadce0;
        }

        .lab-mini-calendar .fc-daygrid-day:not(.fc-day-today):not(.fc-day-other):hover .fc-daygrid-day-number {
            background-color: #f1f3f4;
        }

        .lab-mini-calendar .fc-daygrid-day.fc-day-today {
            background: transparent !important;
        }

        .lab-mini-calendar .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            background-color: #e8f0fe;
            color: #1967d2;
            font-weight: 500;
        }

        .lab-mini-calendar .fc-daygrid-day.is-selected .fc-daygrid-day-number {
            background-color: #1a73e8;
            color: #ffffff;
            font-weight: 500;
        }

        .lab-mini-calendar .fc-daygrid-day.fc-day-today.is-selected .fc-daygrid-day-number {
            background-color: #1a73e8;
            color: #ffffff;
        }

        .lab-mini-calendar .fc-daygrid-day-events,
        .lab-mini-calendar .fc-daygrid-event-harness,
        .lab-mini-calendar .fc-event,
        .lab-mini-calendar .fc-daygrid-day-bg {
            display: none !important;
        }

        .lab-mini-calendar .fc-day-sun .fc-daygrid-day-number {
            color: #d93025;
        }

        .lab-mini-calendar .fc-day-sun.fc-day-other .fc-daygrid-day-number {
            color: #fce8e6;
        }

        .lab-mini-calendar .fc-scrollgrid,
        .lab-mini-calendar .fc-scrollgrid table,
        .lab-mini-calendar .fc-scrollgrid thead,
        .lab-mini-calendar .fc-scrollgrid tbody,
        .lab-mini-calendar .fc-scrollgrid td,
        .lab-mini-calendar .fc-scrollgrid th {
            border: none !important;
        }

        .lab-mini-calendar .fc-scrollgrid {
            box-shadow: none !important;
            outline: none !important;
        }



        @media (max-width: 992px) {
            .lab-layout {
                flex-direction: column;
            }

            .lab-sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .lab-calendar-page {
                padding: 12px;
            }

            .lab-calendar-shell {
                max-width: 100%;
            }

            .form-layout-grid {
                grid-template-columns: 1fr;
            }

            .time-inputs {
                grid-template-columns: 1fr 1fr;
            }

            .modal-content {
                max-width: 100%;
            }
        }
    </style>

    <div class="lab-calendar-page">
        <div class="lab-calendar-shell">
            <div class="lab-layout">

                <aside class="lab-sidebar">
                    @auth
                        <button type="button" class="create-btn js-open-create-event">
                            <i class="fa-solid fa-plus"></i>
                            <span>Tạo sự kiện</span>
                        </button>
                    @endauth
                    <div class="lab-mini-calendar" id="miniCalendar"></div>

                    <div class="lab-sidebar-section">
                        <div class="lab-sidebar-section-title">Trạng thái sự kiện</div>
                        <div class="lab-checklist">
                            <label class="lab-check-item">
                                <input type="checkbox" checked data-filter-status="pending">
                                <span class="lab-check-color status-pending"></span>
                                <span class="lab-check-label">Chờ duyệt</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" checked data-filter-status="approved">
                                <span class="lab-check-color status-approved"></span>
                                <span class="lab-check-label">Đã duyệt</span>
                            </label>
                        </div>
                    </div>

                    <div class="lab-sidebar-section">
                        <div class="lab-sidebar-section-title">Loại sự kiện</div>
                        <div class="lab-checklist">
                            <label class="lab-check-item">
                                <input type="checkbox" checked data-filter-category="work">
                                <span class="lab-check-color cat-work"></span>
                                <span class="lab-check-label">Làm việc / nghiên cứu</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" checked data-filter-category="seminar">
                                <span class="lab-check-color cat-seminar"></span>
                                <span class="lab-check-label">Hội thảo / seminar</span>
                            </label>
                            <label class="lab-check-item">
                                <input type="checkbox" checked data-filter-category="other">
                                <span class="lab-check-color cat-other"></span>
                                <span class="lab-check-label">Khác</span>
                            </label>
                        </div>
                    </div>
                </aside>

                <div class="lab-calendar-main">
                    <div class="lab-calendar-card">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <div id="eventModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Tạo sự kiện mới</h2>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="eventId">

                        <div class="form-layout-grid">
                            <div class="form-group full-width">
                                <label>Tiêu đề sự kiện <span style="color:#d93025">*</span></label>
                                <input type="text" id="eventTitle" required placeholder="Ví dụ: Họp Lab, Seminar...">
                            </div>

                            <div class="form-group">
                                <label>Loại sự kiện <span style="color:#d93025">*</span></label>
                                <select id="eventCategory" required>
                                    <option value="work">Làm việc / nghiên cứu</option>
                                    <option value="seminar">Hội thảo / seminar</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Ngày & giờ bắt đầu <span style="color:#d93025">*</span></label>
                                <div class="time-inputs">
                                    <input type="date" id="eventStartDate" required>
                                    <input type="time" id="eventStartTime" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Ngày & giờ kết thúc <span style="color:#d93025">*</span></label>
                                <div class="time-inputs">
                                    <input type="date" id="eventEndDate" required>
                                    <input type="time" id="eventEndTime" required>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Tài liệu đính kèm (slide / PDF / docs)</label>
                                <div class="file-upload-row">
                                    <input type="file" id="eventFiles" name="files[]" multiple
                                        accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">

                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Mô tả</label>
                                <textarea id="eventDescription" rows="3"
                                    placeholder="Thêm mô tả chi tiết (không bắt buộc)"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="saveEvent()">
                        <i class="fa-regular fa-floppy-disk"></i>
                        <span>Lưu</span>
                    </button>
                </div>
            </div>
        </div>
    @endauth
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="event-details">
                <h2 id="detailTitle"></h2>

                <div class="detail-row">
                    <span class="detail-icon">🕒</span>
                    <span id="detailTime"></span>
                </div>

                <div class="detail-row" id="detailDescriptionRow" style="display: none;">
                    <span class="detail-icon">📝</span>
                    <span id="detailDescription"></span>
                </div>

                <div class="detail-row">
                    <span class="detail-icon">🏷️</span>
                    <span id="detailCategory"></span>
                </div>

                <div class="detail-row" id="detailStatusRow">
                    <span class="detail-icon">
                        <i id="statusPendingIcon" class="fa-solid fa-clock" style="color:#ffc107; display:none;"></i>
                        <i id="statusApprovedIcon" class="fa-solid fa-circle-check"
                            style="color:#28a745; display:none;"></i>
                    </span>
                    <span id="detailStatus"></span>
                </div>
            </div>

            <div class="modal-footer">
                @auth
                    <button type="button" class="btn btn-danger" onclick="deleteEvent()">
                        <i class="fa-regular fa-trash-can"></i>
                        <span>Xóa</span>
                    </button>
                    <button type="button" class="btn btn-primary" onclick="editEvent()">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span>Sửa</span>
                    </button>
                @endauth
                <button type="button" class="btn btn-secondary" onclick="closeDetailModal()">Đóng</button>
            </div>
        </div>
    </div>

    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 16px; font-weight: 600; color: var(--lab-text-main); margin: 0;">
                    Xác nhận xóa sự kiện
                </h2>
            </div>
            <div class="modal-body">
                <p style="color: var(--lab-text-muted); font-size: 13px; margin: 0;">
                    Bạn có chắc chắn muốn xóa sự kiện này không?<br>
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmDelete()">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Xóa</button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        window.LAB_USER = @json([
            'logged_in' => auth()->check(),
            'is_admin' => auth()->check() && auth()->user()->code === 'admin',
        ]);
    </script>

    <script src="{{ asset('assets/js/calendar.js') }}"></script>
</div>