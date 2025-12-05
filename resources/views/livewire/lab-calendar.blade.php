
<div>
    <style>
        .create-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
            transition: all 0.2s;
        }

        .create-btn:hover {
            background: #1765cc;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,0.3), 0 4px 8px 3px rgba(60,64,67,0.15);
        }

        .container {
            display: flex;
            height: calc(100vh - 65px);
        }

        .sidebar {
            width: 260px;
            padding: 16px;
            border-right: 1px solid #dadce0;
        }

        .mini-calendar {
            margin-bottom: 24px;
        }

        .categories {
            margin-top: 16px;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .category-item:hover {
            background: #f1f3f4;
        }

        .category-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .category-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .main-content {
            flex: 1;
            overflow: auto;
            padding: 16px;
        }

        #calendar {
            background: white;
            border-radius: 8px;
        }

        .fc {
            font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
        }

        .fc .fc-button {
            background: white;
            color: #3c4043;
            border: 1px solid #dadce0;
            padding: 8px 16px;
            text-transform: none;
            font-weight: 500;
            box-shadow: none;
        }

        .fc .fc-button:hover {
            background: #f8f9fa;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: #e8f0fe;
            color: #1a73e8;
            border-color: #1a73e8;
        }

        .fc .fc-toolbar-title {
            font-size: 22px;
            font-weight: 400;
            color: #3c4043;
        }

        .fc-event {
            border: none;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 12px;
            cursor: pointer;
        }

        .fc-event:hover {
            opacity: 0.9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 720px;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 8px 10px 1px rgba(0,0,0,0.14), 0 3px 14px 2px rgba(0,0,0,0.12);
        }

        .modal-header {
            padding: 24px 24px 16px;
            border-bottom: 1px solid #e8eaed;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #5f6368;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.1);
        }

        .modal-footer {
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            border-top: 1px solid #e8eaed;
        }

        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #1a73e8;
            color: white;
        }

        .btn-primary:hover {
            background: #1765cc;
        }

        .btn-secondary {
            background: white;
            color: #1a73e8;
            border: 1px solid #dadce0;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
        }

        .btn-danger {
            background: #d93025;
            color: white;
        }

        .btn-danger:hover {
            background: #c5221f;
        }

        .event-details {
            padding: 24px;
        }

        .event-details h2 {
            font-size: 22px;
            margin-bottom: 16px;
            color: #3c4043;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .detail-icon {
            width: 20px;
            text-align: center;
            color: #5f6368;
        }

        .time-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
    </style>
        <div class="main-content">
            <div id="calendar"></div>
        </div>
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="font-size: 22px; font-weight: 400;">Tạo sự kiện mới</h2>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId">

                    <div class="form-group">
                        <label>Tiêu đề sự kiện *</label>
                        <input type="text" id="eventTitle" required placeholder="Nhập tiêu đề">
                    </div>

                    <div class="form-group">
                        <label>Loại sự kiện *</label>
                        <select id="eventCategory" required>
                            <option value="work">Làm việc - nghiên cứu</option>
                            <option value="seminar">Hội thảo - Seminar</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <div class="time-inputs">
                        <div class="form-group">
                            <label>Ngày bắt đầu *</label>
                            <input type="date" id="eventStartDate" required>
                        </div>
                        <div class="form-group">
                            <label>Giờ bắt đầu *</label>
                            <input type="time" id="eventStartTime" required>
                        </div>
                    </div>

                    <div class="time-inputs">
                        <div class="form-group">
                            <label>Ngày kết thúc *</label>
                            <input type="date" id="eventEndDate" required>
                        </div>
                        <div class="form-group">
                            <label>Giờ kết thúc *</label>
                            <input type="time" id="eventEndTime" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea id="eventDescription" rows="3" placeholder="Thêm mô tả (tùy chọn)"></textarea>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveEvent()">Lưu</button>
            </div>
        </div>
    </div>
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="event-details">
                <h2 id="detailTitle"></h2>
                <div class="detail-row">
                    <span class="detail-icon">🕒</span>
                    <span id="detailTime"></span>
                </div>
                <div class="detail-row" id="detailLocationRow" style="display: none;">
                    <span class="detail-icon">📍</span>
                    <span id="detailLocation"></span>
                </div>
                <div class="detail-row" id="detailDescriptionRow" style="display: none;">
                    <span class="detail-icon">📝</span>
                    <span id="detailDescription"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-icon">🏷️</span>
                    <span id="detailCategory"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="deleteEvent()">Xóa</button>
                <button type="button" class="btn btn-primary" onclick="editEvent()">Sửa</button>
                <button type="button" class="btn btn-secondary" onclick="closeDetailModal()">Đóng</button>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/calendar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
</div>
