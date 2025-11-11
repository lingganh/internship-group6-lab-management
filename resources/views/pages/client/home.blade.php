<x-client-layout>
    <x-slot name="custom_js">
        <script src="{{ asset('assets/js/client-calendar.js') }}"></script>
    </x-slot>
    <div class="content">
        <!-- Basic view -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thời khóa biểu phòng LAB</h5>
            </div>

            <div class="card-body">
                <div class="fullcalendar-basic"></div>
                <div class="note-calendar">
                    <div class="note-item">
                        <div class="bullet note-work"></div>
                        <div class="text">Làm việc - nghiên cứu</div>
                    </div>
                    <div class="note-item">
                        <div class="bullet note-seminar"></div>
                        <div class="text">Hội thảo - Seminar</div>
                    </div>
                    <div class="note-item">
                        <div class="bullet note-other"></div>
                        <div class="text">Khác</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /basic view -->
    </div>
</x-client-layout>
