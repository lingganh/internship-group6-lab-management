<div>
    <link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

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

                    <div class="lab-sidebar-section">
                        <div class="lab-sidebar-section-title">Phòng Lab</div>
                        <select id="labRoomFilter" class="lab-room-filter-select">
                            <option value="">Tất cả phòng</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->code }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
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
    <!-- Header -->
    <div class="modal-header">
      <h2 id="modalTitle">Tạo sự kiện mới</h2>
    </div>

    <!-- Body -->
    <div class="modal-body">
      <form id="eventForm">
        <input type="hidden" id="eventId">

        <div class="form-layout-grid">
          <!-- Tiêu đề -->
          <div class="form-group full-width">
            <label>Tiêu đề sự kiện <span style="color:#d93025">*</span></label>
            <input type="text" id="eventTitle" required placeholder="Ví dụ: Họp Lab, Seminar...">
          </div>

          <!-- Loại sự kiện -->
          <div class="form-group">
            <label>Loại sự kiện <span style="color:#d93025">*</span></label>
            <select id="eventCategory" required>
              <option value="work">Làm việc / nghiên cứu</option>
              <option value="seminar">Hội thảo / seminar</option>
              <option value="other">Khác</option>
            </select>
          </div>

          <!-- Màu sắc -->
          <div class="form-group">
            <label>Màu sắc sự kiện</label>
            <div class="google-color-picker">
              <div class="color-grid">
                @php
                  $googleColors = ['#d50000', '#e67c73', '#f4511e', '#f6bf26', '#8e24aa'];
                @endphp

                @foreach($googleColors as $hex)
                  <div class="color-circle" data-color="{{ $hex }}" style="background-color: {{ $hex }};">
                    <i class="fa-solid fa-check"></i>
                  </div>
                @endforeach

                <div class="color-circle custom-color-btn" id="customColorWrapper" title="Màu tùy chỉnh">
                  <i class="fa-solid fa-plus" style="display: block; color: #5f6368;"></i>
                  <input
                    type="color"
                    id="eventColor"
                    value="#039be5"
                    style="opacity: 0; position: absolute; width: 0; height: 0;"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Phòng -->
          <div class="form-group">
            <label>Phòng Lab / Phòng sử dụng <span style="color:#d93025">*</span></label>
            <select id="eventRoom" required>
              <option value="">— Chọn phòng —</option>
              @foreach($rooms as $room)
                <option value="{{ $room->code }}">{{ $room->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Thời gian bắt đầu -->
          <div class="form-group">
            <label>Ngày & giờ bắt đầu <span style="color:#d93025">*</span></label>
            <div class="time-inputs">
              <input type="date" id="eventStartDate" required>
              <input type="time" id="eventStartTime" required>
            </div>
          </div>

          <!-- Thời gian kết thúc -->
          <div class="form-group">
            <label>Ngày & giờ kết thúc <span style="color:#d93025">*</span></label>
            <div class="time-inputs">
              <input type="date" id="eventEndDate" required>
              <input type="time" id="eventEndTime" required>
            </div>
          </div>

          <!-- File -->
          <div class="form-group full-width">
            <label>Tài liệu đính kèm (slide / PDF / docs)</label>
            <input
              type="file"
              id="eventFiles"
              name="files[]"
              multiple
              accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
            >
          </div>

          <!-- Mô tả -->
          <div class="form-group full-width">
            <label>Mô tả</label>
            <textarea
              id="eventDescription"
              rows="3"
              placeholder="Thêm mô tả chi tiết (không bắt buộc)"
            ></textarea>
          </div>
        </div>
      </form>
    </div>

    <!-- Footer -->
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
                <div class="detail-row">
                    <span class="detail-icon">📍</span>
                    <span id="detailRoom"></span>
                </div>
                <div class="detail-row" id="detailDescriptionRow" style="display: none;">
                    <span class="detail-icon">📝</span>
                    <span id="detailDescription"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-icon">🏷️</span>
                    <span id="detailCategory"></span>
                </div>
                <div class="detail-row">
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
                <h2>Xác nhận xóa sự kiện</h2>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sự kiện này không?<br>Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmDelete()">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Xóa</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.LAB_USER = @json(['logged_in' => auth()->check(), 'is_admin' => auth()->check() && auth()->user()->code === 'admin']);
        window.LAB_ROOMS = @json($rooms->map(fn($r) => ['code' => $r->code, 'name' => $r->name])->values());
    </script>
    <script src="{{ asset('assets/js/calendar.js') }}"></script>
</div>