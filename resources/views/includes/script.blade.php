{{--<!-- Core JS files -->--}}
<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
{{--<!-- /core JS files -->--}}

{{--<!-- Theme JS files -->--}}
<script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/noty/noty.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/notifications/sweet_alert.min.js') }}"></script>
{{--<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>--}}
<script src="{{ asset('assets/js/money/simple.money.format.js') }}"></script>
<script src="{{ asset('assets/js/vendor/ui/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickers/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickers/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/notifications/noty.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/ui/fullcalendar/main.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{--<!-- /theme JS files -->--}}


{{--@vite('resources/js/app.js')--}}

{{--<!-- JS custom -->--}}


@yield('script_custom')
{{--<!-- /JS custom  -->--}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Session Flash Messages
        @if (session('success'))
        showNoty('success', "{{ session('success') }}");
        @endif

        @if (session('error'))
        showNoty('error', "{{ session('error') }}");
        @endif

        @if (session('warning'))
        showNoty('warning', "{{ session('warning') }}");
        @endif

        @if (session('info'))
        showNoty('info', "{{ session('info') }}");
        @endif

        // Realtime Livewire Flash Messages
        if (typeof Livewire !== 'undefined') {
            Livewire.on('alert', ({ type, message }) => {
                showNoty(type, message);
            });
        }
    });

    function showNoty(type, message) {
        new Noty({
            type: type,
            layout: 'topRight',
            text: message,
            timeout: 2000,
            progressBar: true,
            closeWith: ['button'],
            callbacks: {
                onTemplate: function() {
                    let color = '#188251'; // Default: success green
                    if (type === 'error') color = '#D9534F'; // Red
                    if (type === 'warning') color = '#FFC107'; // Yellow
                    if (type === 'info') color = '#17A2B8'; // Blue
                    this.barDom.innerHTML = '<div class="noty_body" style="background: ' + color + '; color: #ffffff;">' + this.options.text + '</div>';
                    this.barDom.style.backgroundColor = 'transparent';
                }
            }
        }).show();
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('openModel', ({type,title,desc,confirmEvent}) => {
            console.log(type)
            Swal.fire({
                title: title,
                icon: type,
                text: desc,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Có!",
                cancelButtonText: "Không!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch(confirmEvent);
                }
            });
        });
    });
</script>
