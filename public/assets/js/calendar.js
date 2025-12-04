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
                category: event.category,
                backgroundColor: categoryColors[event.category],
                borderColor: categoryColors[event.category],
                description: event.description
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
         // const categoryKey = event.category;
         // const color = categoryColors[categoryKey];
         // console.log(`Event: "${event.title}", Category: "${categoryKey}", Found Color: "${color}"`);
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
//create+update
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
        alert('Vui lòng nhập tiêu đề sự kiện');
        return;
    }
    const API_URL ='/api/bookings';
    const eventData = {
         title,
        start: `${startDate}T${startTime}:00`,
        end: `${endDate}T${endTime}:00`,
        category,
        description,

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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(eventData)
        })
        if (!response.ok) {
            const data = await response.json();
            console.log(data.errors);
            throw new Error(data.errors);
        }
        const savedEvent = await response.json();
        if(id){
            const index = events.findIndex(e => e.id == id);
            if (index !== -1) {
                events[index] = savedEvent;            }
        }else{
            events.push(savedEvent);
        }
        updateCalendar();
        calendar.refetchEvents();
        closeModal();
    } catch (error) {
        console.log(error);
    }



}

function showEventDetail(calendarEvent) {
    const props = calendarEvent.extendedProps;
    const title = calendarEvent.title;
    const startDate =  calendarEvent.start;
    const endDate =calendarEvent.end;
    const category = props.category;
    const description = props.description;

    // luu cho sua va xoa
    currentEventId = calendarEvent.id;
    //format time
    document.getElementById('detailTime').textContent = `${startDate.toLocaleDateString('vi-VN')} ${startDate.toLocaleTimeString('vi-VN', {
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
    document.getElementById('detailCategory').textContent = categoryNames[category];
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

async function deleteEvent() {
    if (!confirm('Bạn có chắc muốn xóa sự kiện này?')) return;
    const response = await fetch('api/bookings/' + currentEventId,{
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    events = events.filter(e => e.id != currentEventId);
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

