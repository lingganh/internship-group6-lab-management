let calendar;
let events = [];
let currentEventId = null;
let hiddenCategories = new Set();

const categoryColors = {
    work: '#c6006a',
    seminar: '#5b11ab',
    other: '#8baf7e'
};

// màu theo trạng thái
const statusColors = {
    pending: '#ffc107',  // vàng: chờ duyệt
    approved: '#28a745', // xanh: đã duyệt
};

const categoryNames = {
    work: 'Làm việc - nghiên cứu',
    seminar: 'Hội thảo - Seminar',
    other: 'Khác'
};

function initCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

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
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        height: 'auto',
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

    loadEvent();
    calendar.render();
}

// ================== LOAD EVENTS ==================

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
            const isApproved = event.status === 'approved';
            const bgColor = isApproved
                ? (categoryColors[event.category] || statusColors.approved)
                : statusColors.pending;

            return {
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                category: event.category,
                description: event.description,
                status: event.status,
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

    const visibleEvents = events.filter(e => !hiddenCategories.has(e.category));

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
                status: event.status
            }
        });
    });
}

// ================== MODAL CREATE / EDIT ==================

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

// create + update
async function saveEvent() {
    const id = document.getElementById('eventId').value;
    const title = document.getElementById('eventTitle').value.trim();
    const category = document.getElementById('eventCategory').value;
    const startDate = document.getElementById('eventStartDate').value;
    const startTime = document.getElementById('eventStartTime').value;
    const endDate = document.getElementById('eventEndDate').value;
    const endTime = document.getElementById('eventEndTime').value;
    const description = document.getElementById('eventDescription').value.trim();

    if (!title) {
        toastr && toastr.error('Vui lòng nhập tiêu đề sự kiện');
        return;
    }

    const API_URL = '/bookings';
    const eventData = {
        title,
        start: `${startDate}T${startTime}:00`,
        end: `${endDate}T${endTime}:00`,
        category,
        description
    };

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
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify(eventData)
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 401) {
                toastr && toastr.error(result.message || 'Bạn cần đăng nhập để đăng ký sự kiện.');
                return;
            }

            const msg =
                (result && (result.message || (result.errors && Object.values(result.errors)[0][0]))) ||
                'Có lỗi xảy ra, vui lòng thử lại.';
            toastr && toastr.error(msg);
            console.error('Save event error: ', result);
            return;
        }

        // backend: { type, message, data } hoặc trả thẳng event
        const savedEvent = result.data || result;
        const msg = result.message || (id ? 'Cập nhật sự kiện thành công.' : 'Tạo sự kiện thành công.');

        toastr && toastr.success(msg);

        if (id) {
            const index = events.findIndex(e => e.id == id);
            if (index !== -1) {
                events[index] = {
                    ...events[index],
                    ...savedEvent
                };
            }
        } else {
            events.push(savedEvent);
        }

        updateCalendar();
        closeModal();
    } catch (error) {
        console.error(error);
        toastr && toastr.error('Lỗi kết nối máy chủ. Vui lòng thử lại sau.');
    }
}

// ================== MODAL DETAIL ==================

function showEventDetail(calendarEvent) {
    const props = calendarEvent.extendedProps;
    const title = calendarEvent.title;
    const startDate = calendarEvent.start;
    const endDate = calendarEvent.end;
    const category = props.category;
    const description = props.description;
    const status = props.status;

    currentEventId = calendarEvent.id;

    document.getElementById('detailTime').textContent =
        `${startDate.toLocaleDateString('vi-VN')} ${startDate.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        })} - ${endDate.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}`;

    if (description) {
        document.getElementById('detailDescription').textContent = description;
        document.getElementById('detailDescriptionRow').style.display = 'flex';
    } else {
        document.getElementById('detailDescriptionRow').style.display = 'none';
    }

    document.getElementById('detailTitle').textContent = title;
    document.getElementById('detailCategory').textContent = categoryNames[category] || category;

    const statusEl = document.getElementById('detailStatus');
    if (statusEl) {
        statusEl.textContent = status === 'approved' ? 'Đã duyệt' : 'Chờ duyệt';
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

// ================== DELETE ==================

async function deleteEvent() {
    if (!confirm('Bạn có chắc muốn xóa sự kiện này?')) return;

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

        events = events.filter(e => e.id != currentEventId);
        updateCalendar();
        closeDetailModal();
    } catch (err) {
        console.error(err);
        toastr && toastr.error('Lỗi kết nối máy chủ.');
    }
}

// ================== UPDATE TIME (DRAG/RESIZE) ==================

function updateEventTime(calendarEvent) {
    const event = events.find(e => e.id === calendarEvent.id);
    if (event) {
        event.start = calendarEvent.start.toISOString();
        event.end = calendarEvent.end.toISOString();
        // nếu muốn gửi lên server khi kéo thả thì viết thêm fetch PUT ở đây
    }
}

// ================== INIT & CLICK OUTSIDE ==================

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
