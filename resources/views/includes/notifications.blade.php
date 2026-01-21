@php
    use App\Models\User;
    use Illuminate\Support\Facades\Storage;

    $currentUser = auth()->user();

    $notifications = collect();
    $newNotifications = collect();
    $oldNotifications = collect();
    $unreadCount = 0;

    $senders = collect();

    // giới hạn hiển thị
    $limit = 5;
    $newLimited = collect();
    $newRest = collect();
    $newMore = 0;

    $oldLimited = collect();
    $oldRest = collect();
    $oldMore = 0;

    if ($currentUser) {
        // lấy nhiều hơn để "xem thêm" có dữ liệu
        $notifications = $currentUser->notifications()->latest()->take(50)->get();

        $newNotifications = $notifications->whereNull('read_at');
        $oldNotifications = $notifications->whereNotNull('read_at');
        $unreadCount = $newNotifications->count();

        // tính list giới hạn + phần còn lại
        $newLimited = $newNotifications->take($limit);
        $newRest = $newNotifications->slice($limit);
        $newMore = max(0, $newNotifications->count() - $newLimited->count());

        $oldLimited = $oldNotifications->take($limit);
        $oldRest = $oldNotifications->slice($limit);
        $oldMore = max(0, $oldNotifications->count() - $oldLimited->count());

        // load sender để show avatar/name
        $senderIds = $notifications->pluck('data.sender_id')->filter()->unique()->all();
        if (!empty($senderIds)) {
            $senders = User::whereIn('id', $senderIds)->get()->keyBy('id');
        }
    }
@endphp


{{-- OFFCANVAS --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="notifications" aria-modal="true" role="dialog">
    <div class="offcanvas-header py-0">
        <h5 class="offcanvas-title py-3">Thông báo</h5>

        <div class="d-flex align-items-center gap-2">
            @if ($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="me-2">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0">
                        Đọc tất cả
                    </button>
                </form>
            @endif

            <button type="button" class="btn btn-light btn-sm btn-icon border-transparent rounded-pill"
                data-bs-dismiss="offcanvas">
                <i class="ph-x"></i>
            </button>
        </div>
    </div>

    <div class="offcanvas-body p-0">
        {{-- NEW NOTIFICATIONS --}}
        <div class="bg-light fw-medium py-2 px-3">Thông báo mới</div>
        <div class="p-3" id="notificationNewList">
            @forelse($newLimited as $notif)
                @php
                    $senderId = $notif->data['sender_id'] ?? null;
                    $sender = $senderId ? $senders[$senderId] ?? null : null;
                    $senderName = $sender->full_name ?? ($notif->data['sender_name'] ?? 'Hệ thống');
                    $priority = $notif->data['priority'] ?? null;

                    $openUrl = route('notifications.open', $notif);
                @endphp

                <a href="{{ $openUrl }}"
                    class="notification-item notification-unread d-flex align-items-start mb-3 text-reset text-decoration-none">
                    {{-- Avatar người gửi --}}
                    <div class="status-indicator-container me-3">
                        @php $thumb = $sender?->thumbnail; @endphp

                        @if ($sender && filled($thumb))
                            <img src="{{ Storage::url($thumb) }}" class="w-40px h-40px rounded-pill" alt="">
                            <span class="status-indicator bg-success"></span>
                        @elseif ($sender)
                            <img src="{{ Avatar::create($senderName)->toBase64() }}" class="w-40px h-40px rounded-pill"
                                alt="">
                            <span class="status-indicator bg-success"></span>
                        @else
                            <img src="{{ asset('assets/images/default-user-image.png') }}"
                                class="w-40px h-40px rounded-pill" alt="">
                        @endif
                    </div>

                    <div class="flex-fill">
                        <span class="fw-semibold text-body">{{ $senderName }}</span>
                        <span class="ms-1 text-body notif-title">
                            {{ $notif->title }}
                            @if ($priority)
                                (Ưu tiên: {{ $priority }})
                            @endif
                        </span>

                        @if ($notif->message)
                            <div class="mt-1">
                                <span class="text-muted">Tiêu đề:</span>
                                <span class="fw-semibold">{{ $notif->message }}</span>
                            </div>
                        @endif

                        <div class="fs-sm text-muted mt-1">
                            {{ $notif->created_at?->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <div id="notificationNewEmpty" class="text-muted fs-sm">
                    Không có thông báo mới.
                </div>
            @endforelse

            @if ($newMore > 0)
                <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#moreNewNotifications" aria-expanded="false" aria-controls="moreNewNotifications">
                    Xem thêm ({{ $newMore }})
                </button>

                <div class="collapse mt-3" id="moreNewNotifications">
                    @foreach ($newRest as $notif)
                        @php
                            $senderId = $notif->data['sender_id'] ?? null;
                            $sender = $senderId ? $senders[$senderId] ?? null : null;
                            $senderName = $sender->full_name ?? ($notif->data['sender_name'] ?? 'Hệ thống');
                            $priority = $notif->data['priority'] ?? null;

                            $openUrl = route('notifications.open', $notif);
                        @endphp

                        <a href="{{ $openUrl }}"
                            class="notification-item notification-unread d-flex align-items-start mb-3 text-reset text-decoration-none">
                            <div class="status-indicator-container me-3">
                                @php $thumb = $sender?->thumbnail; @endphp

                                @if ($sender && filled($thumb))
                                    <img src="{{ Storage::url($thumb) }}" class="w-40px h-40px rounded-pill"
                                        alt="">
                                    <span class="status-indicator bg-success"></span>
                                @elseif ($sender)
                                    <img src="{{ Avatar::create($senderName)->toBase64() }}"
                                        class="w-40px h-40px rounded-pill" alt="">
                                    <span class="status-indicator bg-success"></span>
                                @else
                                    <img src="{{ asset('assets/images/default-user-image.png') }}"
                                        class="w-40px h-40px rounded-pill" alt="">
                                @endif
                            </div>

                            <div class="flex-fill">
                                <span class="fw-semibold text-body">{{ $senderName }}</span>
                                <span class="ms-1 text-body notif-title">
                                    {{ $notif->title }}
                                    @if ($priority)
                                        (Ưu tiên: {{ $priority }})
                                    @endif
                                </span>

                                @if ($notif->message)
                                    <div class="mt-1">
                                        <span class="fw-semibold">{{ $notif->message }}</span>
                                    </div>
                                @endif

                                <div class="fs-sm text-muted mt-1">
                                    {{ $notif->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- OLDER NOTIFICATIONS --}}
        <div class="bg-light fw-medium py-2 px-3">Thông báo cũ</div>
        <div class="p-3" id="notificationOldList">
            @forelse($oldLimited as $notif)
                @php
                    $senderId = $notif->data['sender_id'] ?? null;
                    $sender = $senderId ? $senders[$senderId] ?? null : null;
                    $senderName = $sender->full_name ?? ($notif->data['sender_name'] ?? 'Hệ thống');
                    $priority = $notif->data['priority'] ?? null;

                    $openUrl = route('notifications.open', $notif);
                @endphp

                <a href="{{ $openUrl }}"
                    class="notification-item notification-read d-flex align-items-start mb-3 text-reset text-decoration-none opacity-75">
                    <div class="status-indicator-container me-3">
                        @php $thumb = $sender?->thumbnail; @endphp

                        @if ($sender && filled($thumb))
                            <img src="{{ Storage::url($thumb) }}" class="w-40px h-40px rounded-pill" alt="">
                            <span class="status-indicator bg-success"></span>
                        @elseif ($sender)
                            <img src="{{ Avatar::create($senderName)->toBase64() }}" class="w-40px h-40px rounded-pill"
                                alt="">
                            <span class="status-indicator bg-success"></span>
                        @else
                            <img src="{{ asset('assets/images/default-user-image.png') }}"
                                class="w-40px h-40px rounded-pill" alt="">
                        @endif
                    </div>

                    <div class="flex-fill">
                        <span class="fw-semibold text-body">{{ $senderName }}</span>
                        <span class="ms-1 text-body notif-title">
                            {{ $notif->title }}
                            @if ($priority)
                                (Ưu tiên: {{ $priority }})
                            @endif
                        </span>

                        @if ($notif->message)
                            <div class="mt-1">
                                <span class="text-muted">Tiêu đề:</span>
                                <span class="fw-semibold">{{ $notif->message }}</span>
                            </div>
                        @endif

                        <div class="fs-sm text-muted mt-1">
                            {{ $notif->created_at?->diffForHumans() }}
                        </div>
                    </div>
                </a>

            @empty
                <div class="text-muted fs-sm">
                    Không có thông báo cũ.
                </div>
            @endforelse
            @if ($oldMore > 0)
                <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#moreOldNotifications" aria-expanded="false"
                    aria-controls="moreOldNotifications">
                    Xem thêm ({{ $oldMore }})
                </button>

                <div class="collapse mt-3" id="moreOldNotifications">
                    @foreach ($oldRest as $notif)
                        @php
                            $senderId = $notif->data['sender_id'] ?? null;
                            $sender = $senderId ? $senders[$senderId] ?? null : null;
                            $senderName = $sender->full_name ?? ($notif->data['sender_name'] ?? 'Hệ thống');
                            $priority = $notif->data['priority'] ?? null;

                            $openUrl = route('notifications.open', $notif);
                        @endphp

                        <a href="{{ $openUrl }}"
                            class="notification-item notification-read d-flex align-items-start mb-3 text-reset text-decoration-none opacity-75">
                            <div class="status-indicator-container me-3">
                                @php $thumb = $sender?->thumbnail; @endphp

                                @if ($sender && filled($thumb))
                                    <img src="{{ Storage::url($thumb) }}" class="w-40px h-40px rounded-pill"
                                        alt="">
                                    <span class="status-indicator bg-success"></span>
                                @elseif ($sender)
                                    <img src="{{ Avatar::create($senderName)->toBase64() }}"
                                        class="w-40px h-40px rounded-pill" alt="">
                                    <span class="status-indicator bg-success"></span>
                                @else
                                    <img src="{{ asset('assets/images/default-user-image.png') }}"
                                        class="w-40px h-40px rounded-pill" alt="">
                                @endif
                            </div>

                            <div class="flex-fill">
                                <span class="fw-semibold text-body">{{ $senderName }}</span>
                                <span class="ms-1 text-body notif-title">
                                    {{ $notif->title }}
                                    @if ($priority)
                                        (Ưu tiên: {{ $priority }})
                                    @endif
                                </span>

                                @if ($notif->message)
                                    <div class="mt-1">
                                        <span class="text-muted">Tiêu đề:</span>
                                        <span class="fw-semibold">{{ $notif->message }}</span>
                                    </div>
                                @endif

                                <div class="fs-sm text-muted mt-1">
                                    {{ $notif->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
