{{-- <!-- Core JS files --> --}}
<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
{{-- <!-- /core JS files --> --}}

{{-- <!-- Theme JS files --> --}}
<script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/noty/noty.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/notifications/sweet_alert.min.js') }}"></script>
{{-- <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script> --}}
<script src="{{ asset('assets/js/money/simple.money.format.js') }}"></script>
<script src="{{ asset('assets/js/vendor/ui/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickers/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickers/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/notifications/noty.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/ui/fullcalendar/main.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- <!-- /theme JS files --> --}}

{{-- script của service thông báo --}}
<script src="https://unpkg.com/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js"></script>

{{-- @vite('resources/js/app.js') --}}

{{-- <!-- JS custom --> --}}


@yield('script_custom')
{{-- <!-- /JS custom  --> --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            Livewire.on('alert', ({
                type,
                message
            }) => {
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
                    this.barDom.innerHTML = '<div class="noty_body" style="background: ' + color +
                        '; color: #ffffff;">' + this.options.text + '</div>';
                    this.barDom.style.backgroundColor = 'transparent';
                }
            }
        }).show();
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('openModel', ({
            type,
            title,
            desc,
            confirmEvent
        }) => {
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

<script>
    (function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const openBase = "{{ url('/notifications') }}";

        const EchoCtor = window.Echo;
        if (typeof EchoCtor !== 'function') return;

        if (typeof window.Echo.socketId !== 'function') {
            const cfg = window.LM?.reverb ?? {};

            window.Echo = new EchoCtor({
                broadcaster: 'reverb',
                key: cfg.key ?? 'local',
                wsHost: cfg.host ?? window.location.hostname,
                wsPort: cfg.port ?? 8080,
                wssPort: cfg.port ?? 8080,
                forceTLS: (cfg.scheme ?? 'http') === 'https',
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                    }
                }
            });
        }

        const userId = window.LM?.userId;
        if (!userId) return;

        window.Echo.private(`users.${userId}`)
            .listen('.notification.created', (e) => {
                const badge = document.getElementById('notificationUnreadBadge');
                if (badge) {
                    const current = parseInt((badge.textContent || '0').trim(), 10) || 0;
                    const next = current + 1;
                    badge.textContent = next;
                    badge.classList.remove('d-none');
                }

                const list = document.getElementById('notificationNewList');
                const empty = document.getElementById('notificationNewEmpty');
                if (empty) empty.remove();

                if (list) {
                    const fallbackAvatar = "{{ asset('assets/images/default-user-image.png') }}";
                    const openUrl = `${openBase}/${e.id}/open`;

                    const html = `
                    <a href="${openUrl}"
                        class="notification-item notification-unread d-flex align-items-start mb-3 text-reset text-decoration-none">
                        <div class="status-indicator-container me-3">
                            <img src="${e.sender_avatar || fallbackAvatar}" class="w-40px h-40px rounded-pill" alt="">
                            <span class="status-indicator bg-success"></span>
                        </div>

                        <div class="flex-fill">
                            <span class="fw-semibold text-body">${escapeHtml(e.sender_name || 'Hệ thống')}</span>
                            <span class="ms-1 text-body notif-title">${escapeHtml(e.title || '')}</span>
                            ${e.message ? `<div class="mt-1"><span class="fw-semibold">${escapeHtml(e.message)}</span></div>` : ''}
                            <div class="fs-sm text-muted mt-1">Vừa xong</div>
                        </div>
                    </a>
                    `;


                    list.insertAdjacentHTML('afterbegin', html);
                }

                if (typeof showNoty === 'function') {
                    showNoty('info', 'Có thông báo mới');
                }
            });

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/eruda"></script>
<script>eruda.init();</script>
