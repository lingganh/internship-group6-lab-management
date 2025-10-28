/* ------------------------------------------------------------------------------
 *
 *  # Fullcalendar basic options
 *
 *  Demo JS code for extra_fullcalendar_styling.html page
 *
 * ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------

const FullCalendarStyling = function() {


    //
    // Setup module components
    //

    // External events
    const _componentFullCalendarStyling = function() {
        if (typeof FullCalendar == 'undefined') {
            console.warn('Warning - Fullcalendar files are not loaded.');
            return;
        }

        // Event background colors
        const eventBackgroundColors = [
            {
                title: 'Nghiên cứu khoa học',
                daysOfWeek: [5], // Thứ 2, 4, 6
                startTime: '13:30',
                endTime: '17:30',
                startRecur: '2025-09-01',
                endRecur: '2025-12-31'
            },
            {
                title: 'Nhóm ST NKCH',
                daysOfWeek: [6], // Thứ 2, 4, 6
                startTime: '8:30',
                endTime: '17:30',
                startRecur: '2025-09-01',
                endRecur: '2025-12-31'
            },
            {
                id: 1,
                title: 'seminar',
                start: '2025-09-12T15:00:00',
                end: '2025-09-12T16:30:00',
                constraint: 'businessHours',
                color: '#EF5350',
            },
            {
                id: 2,
                title: 'seminar 2',
                start: '2025-09-09T08:00:00',
                end: '2025-09-09T10:30:00',
                constraint: 'businessHours',
                color: '#ff9f89'
            },
        ];


        //
        // Event background colors
        //

        // Define element
        const calendarEventBgColorsElement = document.querySelector('.fullcalendar-basic');
        const initialDate = new Date().toISOString().slice(0, 10);
        console.log(initialDate)

        // Initialize
        if(calendarEventBgColorsElement) {
            const calendarEventBgColorsInit = new FullCalendar.Calendar(calendarEventBgColorsElement, {
                locale: 'vi',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                footerToolbar: {
                    left: 'prev',
                    center: 'today',
                    right: 'next'
                },
                initialDate: initialDate,
                initialView: 'timeGridWeek',
                slotMinTime: '07:00:00',
                slotMaxTime: '21:30:00',
                allDaySlot: false,
                firstDay:1,
                weekNumbers: true,
                navLinks: true, // can click day/week names to navigate views
                // businessHours: true, // display business hours
                editable: false,
                selectable: true,
                select: function(info) {
                    // Lấy thời gian bắt đầu và kết thúc
                    console.log("Start:", info.start);
                    console.log("End:", info.end);

                    // Nếu muốn định dạng đẹp hơn
                    console.log("Start (ISO):", info.startStr);
                    console.log("End (ISO):", info.endStr);

                    // Ví dụ: hiển thị trong alert
                },
                direction: document.dir == 'rtl' ? 'rtl' : 'ltr',
                events: eventBackgroundColors,
                buttonText: {
                    today: 'Hôm nay',
                    month: 'Tháng',
                    week: 'Tuần',
                    day: 'Ngày',
                    // next: 'Sau→',
                    // prev: '←Trước'
                }
            });

            // Init
            calendarEventBgColorsInit.render();

            // Resize calendar when sidebar toggler is clicked
            document.querySelectorAll('.sidebar-control').forEach(function(sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    calendarEventBgColorsInit.updateSize();
                })
            });
        }
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentFullCalendarStyling();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    FullCalendarStyling.init();

});
