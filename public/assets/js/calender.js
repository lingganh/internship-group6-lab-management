let calendar;
let events = [];
let currentEventId = null;
let hiddenCategories = new Set();

const categoryColors = {
    work: '#c6006a',
    seminar: '#5b11ab',
    other: '#8baf7e'
};

const categoryNames = {
    work: 'Làm việc - nghiên cứu',
    seminar: 'Hội thảo - Seminar',
    other: 'Khác'
};

function initCalendar() {
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

    // loadSampleEvents();
    calendar.render();
}

function loadSampleEvents() {
    const now = new Date();
    const today = now.toISOString().split('T')[0];

//     events = [
// {
//     id: '1',
//     title: 'Nghiên cứu AI',
//     start: `${today}T09:00:00`,
//     end: `${today}T11:30:00`,
//     category: 'work',
//     description: 'Nghiên cứu về machine learning',
//     location: 'Phòng LAB 1'
// },
// {
//     id: '2',
//     title: 'Hội thảo Blockchain',
//     start: `${today}T14:00:00`,
//     end: `${today}T17:00:00`,
//     category: 'seminar',
//     description: 'Hội thảo về công nghệ blockchain',
//     location: 'Hội trường A'
// }
//     ];
    updateCalendar();
}

async function loadEvent() {
    const response = await fetch('api/bookings');
    const data = await response.json();

    events = data.map(
        event => {
            return {
                id: event.id,
                title: event.title,
                start: event.start,
                end: event.end,
                backgroundColor: categoryColors[event.category],
                borderColor: categoryColors[event.category],
            }
        }
    )

    updateCalendar();
}

function updateCalendar() {
    calendar.removeAllEvents();
    // xóa hết tất cả các event -> mỗi lần thêm mới render
    // tạo 1 mảng -> lọc add event
    const visibleEvents = events.filter(e => !hiddenCategories.has(e.category));
    visibleEvents.forEach(event => {

        calendar.addEvent({
            //cấu trúc 1 e
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            backgroundColor: categoryColors[event.category],
            borderColor: categoryColors[event.category],
            extendedProps: {
                category: event.category,
                description: event.description,
            }
        });
    });
}

// modal
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

function saveEvent() {
    const id = document.getElementById('eventId').value;
    const title = document.getElementById('eventTitle').value.trim();
    const category = document.getElementById('eventCategory').value;
    const startDate = document.getElementById('eventStartDate').value;
    const startTime = document.getElementById('eventStartTime').value;
    const endDate = document.getElementById('eventEndDate').value;
    const endTime = document.getElementById('eventEndTime').value;
    const description = document.getElementById('eventDescription').value.trim();

    if (!title) {
        alert('Vui lòng nhập tiêu đề sự kiện');
        return;
    }

    const event = {
        id: id || Date.now().toString(),
        title,
        start: `${startDate}T${startTime}:00`,
        end: `${endDate}T${endTime}:00`,
        category,
        description,

    };

    if (id) {
        const index = events.findIndex(e => e.id === id);
        if (index !== -1) {
            events[index] = event;
        }
    } else {
        events.push(event);
    }

    updateCalendar();
    closeModal();
}

function showEventDetail(calendarEvent) {
    const event = events.find(e => e.id === calendarEvent.id);
    if (!event) return;

    currentEventId = event.id;
    document.getElementById('detailTitle').textContent = event.title;

    const startDate = new Date(event.start);
    const endDate = new Date(event.end);
    document.getElementById('detailTime').textContent = `${startDate.toLocaleDateString('vi-VN')} ${startDate.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit'
    })} - ${endDate.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}`;


    if (event.description) {
        document.getElementById('detailDescription').textContent = event.description;
        document.getElementById('detailDescriptionRow').style.display = 'flex';
    } else {
        document.getElementById('detailDescriptionRow').style.display = 'none';
    }

    document.getElementById('detailCategory').textContent = categoryNames[event.category];
    document.getElementById('detailModal').classList.add('active');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('active');
    currentEventId = null;
}

function editEvent() {
    const event = events.find(e => e.id === currentEventId);
    if (!event) return;

    closeDetailModal();

    document.getElementById('modalTitle').textContent = 'Sửa sự kiện';
    document.getElementById('eventId').value = event.id;
    document.getElementById('eventTitle').value = event.title;
    document.getElementById('eventCategory').value = event.category;

    const startDate = new Date(event.start);
    const endDate = new Date(event.end);

    document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('eventStartTime').value = startDate.toTimeString().slice(0, 5);
    document.getElementById('eventEndDate').value = endDate.toISOString().split('T')[0];
    document.getElementById('eventEndTime').value = endDate.toTimeString().slice(0, 5);
    document.getElementById('eventDescription').value = event.description || '';


    document.getElementById('eventModal').classList.add('active');
}

function deleteEvent() {
    if (!confirm('Bạn có chắc muốn xóa sự kiện này?')) return;

    events = events.filter(e => e.id !== currentEventId);
    updateCalendar();
    closeDetailModal();
}

function updateEventTime(calendarEvent) {
    const event = events.find(e => e.id === calendarEvent.id);
    if (event) {
        event.start = calendarEvent.start.toISOString();
        event.end = calendarEvent.end.toISOString();
    }
}


document.addEventListener('DOMContentLoaded', initCalendar);

document.getElementById('eventModal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});

document.getElementById('detailModal').addEventListener('click', function (e) {
    if (e.target === this) closeDetailModal();
});
