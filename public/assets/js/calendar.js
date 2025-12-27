let calendar;
let events = [];
let currentEventId = null;
let hiddenCategories = new Set();
let hiddenStatuses = new Set();
let selectedRoomFilter = ''; // lọc theo mã phòng

const categoryColors = {
    work: '#bc307bff',
    seminar: '#c4b517ff',
    other: '#4d6d41ff'
};

const statusColors = {
    pending: '#ffc107',
    approved: '#28a745',
};

const categoryNames = {
    work: 'Làm việc - nghiên cứu',
    seminar: 'Hội thảo - Seminar',
    other: 'Khác'
};

 const roomMap = {};
if (window.LAB_ROOMS && Array.isArray(window.LAB_ROOMS)) {
    window.LAB_ROOMS.forEach(r => {
        if (r.code) {
            roomMap[r.code] = r.name || r.code;
        }
    });
}

function initCalendar() {

    initMiniCalendar();

    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    const canCreate = window.LAB_USER && window.LAB_USER.logged_in;

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
        editable: true,
        selectMirror: true,
        selectable: canCreate,
        dayMaxEvents: true,
        weekends: true,
        height: 'auto',
        eventContent: function (arg) {
            const event = arg.event;
            const status = event.extendedProps.status;
            const roomName = event.extendedProps.roomName || '';

            const isApproved = status === 'approved';

            const statusText = isApproved ? 'Đã duyệt' : 'Chờ duyệt';
            const statusClass = isApproved ? 'fc-status-approved' : 'fc-status-pending';
            const statusIcon = isApproved
                ? '<i class="fa-solid fa-circle-check"></i>'
                : '<i class="fa-solid fa-clock"></i>';

            const html = `
        <div class="fc-event-main-custom">
            <div class="fc-event-time">${arg.timeText}</div>
            <div class="fc-event-title">${event.title}</div>
            ${roomName ? `<div class="fc-event-room">${roomName}</div>` : ''}
            <div class="fc-event-status ${statusClass}">
                <span class="fc-status-icon">${statusIcon}</span>
                <span>${statusText}</span>
            </div>
        </div>
    `;

            return { html };
        },

        eventClick: function (info) {
            showEventDetail(info.event);
        },
        select: function (info) {
            openCreateModal(info.start, info.end);
        },
        eventDrop: function (info) {
            updateEventTime(info.event);
        },
        eventResize: function (info) {
            updateEventTime(info.event);
        }
    });

    initFiltersAndButtons();
    loadEvent();
    calendar.render();
}

function initFiltersAndButtons() {
    // trạng thái
    const statusCheckboxes = document.querySelectorAll('[data-filter-status]');
    statusCheckboxes.forEach(cb => {
        const status = cb.getAttribute('data-filter-status');

        if (!cb.checked) {
            hiddenStatuses.add(status);
        }

        cb.addEventListener('change', function () {
            if (this.checked) {
                hiddenStatuses.delete(status);
            } else {
                hiddenStatuses.add(status);
            }
            updateCalendar();
        });
    });

    // loại sự kiện
    const categoryCheckboxes = document.querySelectorAll('[data-filter-category]');
    categoryCheckboxes.forEach(cb => {
        const cat = cb.getAttribute('data-filter-category');

        if (!cb.checked) {
            hiddenCategories.add(cat);
        }

        cb.addEventListener('change', function () {
            if (this.checked) {
                hiddenCategories.delete(cat);
            } else {
                hiddenCategories.add(cat);
            }
            updateCalendar();
        });
    });

    // lọc phòng
    const roomFilterSelect = document.getElementById('labRoomFilter');
    if (roomFilterSelect) {
        roomFilterSelect.addEventListener('change', function () {
            selectedRoomFilter = this.value || '';
            updateCalendar();
        });
    }

    // tạo sự kiện
    const createBtn = document.querySelector('.js-open-create-event');
    if (createBtn) {
        createBtn.addEventListener('click', function () {
            openCreateModal();
        });
    }
}

async function loadEvent() {
    try {
        const response = await fetch('/bookings', {
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        const raw = Array.isArray(data) ? data : (data.data || []);

        events = raw.map(event => {
            const bgColor = categoryColors[event.category] || '#e4f1c4ff';

            const roomCode = event.lab_code || null;  
            const roomName = roomCode ? (roomMap[roomCode] || roomCode) : null;

            return {
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                category: event.category,
                description: event.description,
                status: event.status,
                roomCode: roomCode,
                roomName: roomName,
                backgroundColor: bgColor,
                borderColor: bgColor
            };
        });

        updateCalendar();
    } catch (err) {
        console.error(err);
        if (window.toastr) {
            toastr.error('Không tải được dữ liệu lịch.');
        } else {
            alert('Không tải được dữ liệu lịch.');
        }
    }
}

function updateCalendar() {
    if (!calendar) return;

    calendar.removeAllEvents();

    const visibleEvents = events.filter(e =>
        !hiddenCategories.has(e.category) &&
        !hiddenStatuses.has(e.status) &&
        (!selectedRoomFilter || e.roomCode === selectedRoomFilter)
    );

    visibleEvents.forEach(event => {
        calendar.addEvent({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            backgroundColor: event.backgroundColor || categoryColors[event.category],
            borderColor: event.borderColor || categoryColors[event.category],
            extendedProps: {
                category: event.category,
                description: event.description,
                status: event.status,
                roomCode: event.roomCode,
                roomName: event.roomName
            }
        });
    });
}

function openCreateModal(start = null, end = null) {
    document.getElementById('modalTitle').textContent = 'Tạo sự kiện mới';
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';

    if (start) {
        const startDate = new Date(start);
        document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0];
        document.getElementById('eventStartTime').value = startDate.toTimeString().slice(0, 5);

        if (end) {
            const endDate = new Date(end);
            document.getElementById('eventEndDate').value = endDate.toISOString().split('T')[0];
            document.getElementById('eventEndTime').value = endDate.toTimeString().slice(0, 5);
        }
    } else {
        const now = new Date();
        document.getElementById('eventStartDate').value = now.toISOString().split('T')[0];
        document.getElementById('eventStartTime').value = '09:00';
        document.getElementById('eventEndDate').value = now.toISOString().split('T')[0];
        document.getElementById('eventEndTime').value = '10:00';
    }

    document.getElementById('eventModal').classList.add('active');
}

function closeModal() {
    document.getElementById('eventModal').classList.remove('active');
}

async function saveEvent() {
    const id = document.getElementById('eventId').value;
    const title = document.getElementById('eventTitle').value.trim();
    const category = document.getElementById('eventCategory').value;
    const roomCode = document.getElementById('eventRoom').value.trim();
    const startDate = document.getElementById('eventStartDate').value;
    const startTime = document.getElementById('eventStartTime').value;
    const endDate = document.getElementById('eventEndDate').value;
    const endTime = document.getElementById('eventEndTime').value;
    const description = document.getElementById('eventDescription').value.trim();
    const filesInput = document.getElementById('eventFiles');

    if (!title) {
        toastr && toastr.error('Vui lòng nhập tiêu đề sự kiện');
        return;
    }

    if (!roomCode) {
        toastr && toastr.error('Vui lòng chọn phòng lab / phòng sử dụng');
        return;
    }

    const API_URL = '/bookings';

    const formData = new FormData();
    formData.append('title', title);
    formData.append('category', category);
    formData.append('lab_code', roomCode);  
    formData.append('start', `${startDate}T${startTime}:00`);
    formData.append('end', `${endDate}T${endTime}:00`);
    formData.append('description', description);

    if (filesInput && filesInput.files.length > 0) {
        Array.from(filesInput.files).forEach(file => {
            formData.append('files[]', file);
        });
    }

    try {
        let method = 'POST';
        let url = API_URL;

        if (id) {
            method = 'PUT';
            url = `${API_URL}/${id}`;
        }

        const response = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: formData
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 401) {
                toastr && toastr.error('Bạn cần đăng nhập để đăng ký sự kiện.');
                return;
            }

            const msg =
                (result && (result.message || (result.errors && Object.values(result.errors)[0][0]))) ||
                'Có lỗi xảy ra, vui lòng thử lại.';
            toastr && toastr.error(msg);
            console.error('Save event error: ', result);
            return;
        }

        const savedEvent = result.data || result;
        const msg = result.message || (id ? 'Cập nhật sự kiện thành công.' : 'Tạo sự kiện thành công.');

        toastr && toastr.success(msg);

        if (id) {
            const index = events.findIndex(e => e.id == id);
            if (index !== -1) {
                const roomCodeSaved = savedEvent.lab_code || roomCode;
                events[index] = {
                    ...events[index],
                    ...savedEvent,
                    roomCode: roomCodeSaved,
                    roomName: roomMap[roomCodeSaved] || roomCodeSaved,
                };
            }
        } else {
            const roomCodeSaved = savedEvent.lab_code || roomCode;
            events.push({
                ...savedEvent,
                roomCode: roomCodeSaved,
                roomName: roomMap[roomCodeSaved] || roomCodeSaved,
                backgroundColor: categoryColors[savedEvent.category] || '#e4f1c4ff',
                borderColor: categoryColors[savedEvent.category] || '#e4f1c4ff',
            });
        }

        updateCalendar();
        closeModal();
    } catch (error) {
        console.error(error);
        toastr && toastr.error('Lỗi kết nối máy chủ. Vui lòng thử lại sau.');
    }
}

function showEventDetail(calendarEvent) {
    const props = calendarEvent.extendedProps;
    const title = calendarEvent.title;
    const startDate = calendarEvent.start;
    const endDate = calendarEvent.end;
    const category = props.category;
    const description = props.description;
    const status = props.status;
    const roomName = props.roomName || (props.roomCode ? (roomMap[props.roomCode] || props.roomCode) : null);

    currentEventId = calendarEvent.id;

    document.getElementById('detailTime').textContent =
        `${startDate.toLocaleDateString('vi-VN')} ${startDate.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        })} - ${endDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}`;

    document.getElementById('detailRoom').textContent =
        roomName || 'Phòng máy trung tâm';

    if (description) {
        document.getElementById('detailDescription').textContent = description;
        document.getElementById('detailDescriptionRow').style.display = 'flex';
    } else {
        document.getElementById('detailDescriptionRow').style.display = 'none';
    }

    document.getElementById('detailTitle').textContent = title;
    document.getElementById('detailCategory').textContent = categoryNames[category] || category;

    const statusTextEl = document.getElementById('detailStatus');
    const pendingIcon = document.getElementById('statusPendingIcon');
    const approvedIcon = document.getElementById('statusApprovedIcon');

    pendingIcon.style.display = 'none';
    approvedIcon.style.display = 'none';

    if (status === 'approved') {
        approvedIcon.style.display = 'inline-block';
        statusTextEl.textContent = 'Đã duyệt';
    } else {
        pendingIcon.style.display = 'inline-block';
        statusTextEl.textContent = 'Chờ duyệt';
    }

    document.getElementById('detailModal').classList.add('active');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('active');
    currentEventId = null;
}

function editEvent() {
    const event = events.find(e => e.id == currentEventId);
    if (!event) return;

    closeDetailModal();

    document.getElementById('modalTitle').textContent = 'Sửa sự kiện';
    document.getElementById('eventId').value = event.id;
    document.getElementById('eventTitle').value = event.title;
    document.getElementById('eventCategory').value = event.category;
    document.getElementById('eventDescription').value = event.description || '';
    document.getElementById('eventRoom').value = event.roomCode || '';

    const startDate = new Date(event.start);
    const endDate = new Date(event.end);

    const startLocalDate = `${startDate.getFullYear()}-${String(startDate.getMonth() + 1).padStart(2, '0')}-${String(startDate.getDate()).padStart(2, '0')}`;
    const endLocalDate = `${endDate.getFullYear()}-${String(endDate.getMonth() + 1).padStart(2, '0')}-${String(endDate.getDate()).padStart(2, '0')}`;

    const startTime = startDate.toTimeString().slice(0, 5);
    const endTime = endDate.toTimeString().slice(0, 5);

    document.getElementById('eventStartDate').value = startLocalDate;
    document.getElementById('eventStartTime').value = startTime;
    document.getElementById('eventEndDate').value = endLocalDate;
    document.getElementById('eventEndTime').value = endTime;

    document.getElementById('eventModal').classList.add('active');
}

function deleteEvent() {
    document.getElementById('confirmDeleteModal').classList.add('active');
}

function closeConfirmDelete() {
    document.getElementById('confirmDeleteModal').classList.remove('active');
}

async function confirmDelete() {
    closeConfirmDelete();

    try {
        const response = await fetch('/bookings/' + currentEventId, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            }
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            const msg = result.message || 'Không thể xóa sự kiện.';
            toastr && toastr.error(msg);
            return;
        }

        toastr && toastr.success(result.message || 'Đã xóa sự kiện.');

        await loadEvent();
        closeDetailModal();
    } catch (err) {
        console.error(err);
        toastr && toastr.error('Lỗi kết nối máy chủ.');
    }
}

async function updateEventTime(calendarEvent) {
    const id = calendarEvent.id;

    const start = calendarEvent.start.toISOString();
    const end = calendarEvent.end.toISOString();

    try {
        const response = await fetch(`/bookings/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({ start, end })
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            const msg =
                (result && (result.message || (result.errors && Object.values(result.errors)[0][0]))) ||
                'Không thể cập nhật thời gian.';
            toastr && toastr.error(msg);
            calendarEvent.revert();
            return;
        }

        const index = events.findIndex(e => e.id == id);
        if (index !== -1) {
            events[index] = {
                ...events[index],
                ...result.data
            };
        }

        toastr && toastr.success(result.message || 'Đã cập nhật thời gian sự kiện.');
        updateCalendar();
    } catch (err) {
        console.error(err);
        toastr && toastr.error('Lỗi kết nối khi cập nhật.');
        calendarEvent.revert();
    }
}

document.addEventListener('DOMContentLoaded', initCalendar);

const eventModalEl = document.getElementById('eventModal');
if (eventModalEl) {
    eventModalEl.addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
}

const detailModalEl = document.getElementById('detailModal');
if (detailModalEl) {
    detailModalEl.addEventListener('click', function (e) {
        if (e.target === this) closeDetailModal();
    });
}

function initMiniCalendar() {
    const miniEl = document.getElementById('miniCalendar');
    if (!miniEl) return;

    const mini = new FullCalendar.Calendar(miniEl, {
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
            if (calendar) {
                calendar.gotoDate(info.date);
            }
        }
    });

    mini.render();
}
