@php
    use Carbon\Carbon;
    Carbon::setLocale('vi');
@endphp

<x-client-layout>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-soft: #e0ecff;
            --accent-green: #16a34a;
            --accent-orange: #f97316;
            --accent-red: #ef4444;

            --text-dark: #0f172a;
            --text-gray: #6b7280;
            --text-muted: #9ca3af;

            --bg-page: #f3f4f6;
            --bg-card: #ffffff;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 12px 30px rgba(15, 23, 42, 0.12);
            --radius: 16px;
        }

        .content.seminar {
            background:
                radial-gradient(circle at top left, #dbeafe 0, transparent 55%),
                radial-gradient(circle at bottom right, #fee2e2 0, transparent 55%),
                #f3f4f6;
            padding-bottom: 40px;
        }

        /* Card */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
            background: var(--bg-card);
            overflow: hidden;
            backdrop-filter: blur(6px);
        }

        .card-header {
            background: linear-gradient(90deg, #ffffff 0%, #eff6ff 40%, #dbeafe 100%);
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .card-header h4 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: .16em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h4 i {
            font-size: 18px;
            color: var(--primary-color);
        }

        .seminar-section {
            padding: 14px 16px 20px;
        }

        .seminar-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* ITEM */
        .seminar-item {
            display: flex;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 40%, #eff6ff 100%);
            border-radius: 20px;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            position: relative;
            border: 1px solid rgba(148, 163, 184, 0.35);
        }

        .seminar-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(37, 99, 235, 0.5);
        }

        .seminar-item::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border-left: 4px solid transparent;
            transition: border-color .18s ease;
        }

        .seminar-item:hover::before {
            border-color: var(--primary-color);
        }

        /* MINI CALENDAR */
        .date_stamp {
            width: 110px;
            flex-shrink: 0;
            background: radial-gradient(circle at top, #2563eb 0, #60a5fa 45%, #eff6ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 10px;
            color: #eff6ff;
            position: relative;
        }

        .date_stamp::after {
            content: "";
            position: absolute;
            top: 8px;
            left: 18px;
            right: 18px;
            height: 4px;
            border-radius: 999px;
            background: rgba(239, 246, 255, 0.7);
        }

        .date_stamp_inner {
            margin-top: 8px;
            text-align: center;
        }

        .weekday {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .14em;
            margin-bottom: 4px;
            opacity: .9;
        }

        .event_date {
            font-size: 30px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1px;
        }

        .date_time_bottom {
            font-size: 12px;
            font-weight: 500;
            opacity: .9;
            margin: 0;
        }

        /* RIGHT */
        .seminar_item_right {
            flex: 1;
            padding: 12px 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .article-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .article-title a {
            font-size: 16px;
            font-weight: 650;
            color: var(--text-dark);
            text-decoration: none;
            line-height: 1.5;
            transition: color .18s, transform .18s;
        }

        .seminar-item:hover .article-title a {
            color: var(--primary-color);
            transform: translateY(-1px);
        }

        /* STATUS BADGE */
        .status-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 11px;
            border-radius: 999px;
            letter-spacing: .1em;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .status-today {
            background: rgba(239, 68, 68, 0.06);
            color: var(--accent-red);
            border-color: rgba(239, 68, 68, 0.4);
        }

        .status-tomorrow {
            background: rgba(249, 115, 22, 0.06);
            color: var(--accent-orange);
            border-color: rgba(249, 115, 22, 0.4);
        }

        .status-upcoming {
            background: rgba(34, 197, 94, 0.06);
            color: var(--accent-green);
            border-color: rgba(34, 197, 94, 0.5);
        }

        .status-done {
            background: #f3f4f6;
            color: #4b5563;
            border-color: #d1d5db;
        }

        /* CATEGORY PILL */
        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 4px 9px;
            border-radius: 999px;
            margin-top: 2px;
        }

        .category-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .category-work {
            background: rgba(59, 130, 246, 0.07);
            color: #1d4ed8;
        }

        .category-seminar {
            background: rgba(168, 85, 247, 0.07);
            color: #7c3aed;
        }

        .category-other {
            background: rgba(34, 197, 94, 0.07);
            color: #059669;
        }

        /* META + DESCRIPTION */
        .seminar-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 16px;
            font-size: 13px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        .seminar-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.08);
        }

        .seminar-meta-item i {
            font-size: 13px;
        }

        .seminar-desc {
            margin-top: 4px;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Empty */
        .empty-calendar {
            padding: 40px 24px;
            color: var(--text-gray);
        }

        .empty-calendar img {
            max-width: 160px;
            margin-bottom: 16px;
            opacity: 0.85;
        }

        /* Filter */
        .filter-seminar select {
            padding: 7px 26px 7px 12px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            font-size: 13px;
            background-color: #ffffff;
            min-width: 160px;
        }

        .filter-seminar select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.2);
        }

        @media (max-width: 768px) {
            .seminar-section {
                padding: 12px 10px 18px;
            }

            .seminar-item {
                flex-direction: column;
            }

            .date_stamp {
                width: 100%;
                justify-content: flex-start;
                border-radius: 20px 20px 0 0;
            }

            .seminar_item_right {
                padding: 11px 13px 13px;
            }

            .article-title {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="content seminar">

        {{-- UPCOMING --}}
        <div class="card">
            <div class="card-header">
                <h4>
                    <i class="fa-regular fa-calendar-check"></i>
                    Các sự kiện sắp diễn ra
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="seminar-section">
                    <div class="seminar-list">
                        @forelse($upcomingEvents as $event)
                            @php
                                $categoryClass = 'category-' . ($event->category ?? 'other');
                            @endphp
                            <div class="seminar-item">
                                <div class="date_stamp">
                                    <div class="date_stamp_inner">
                                        <div class="weekday">
                                            {{ $event->start->isoFormat('dddd') }}
                                        </div>
                                        <div class="event_date">
                                            {{ $event->start->format('d') }}
                                        </div>
                                        <p class="date_time_bottom">
                                            {{ $event->start->format('m/Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="seminar_item_right">
                                    <div class="article-title">
                                        <div>
                                            <a href="#">{{ $event->title }}</a>
                                            <div class="category-pill {{ $categoryClass }}">
                                                <span class="category-dot"></span>
                                                {{ ucfirst($event->category) }}
                                            </div>
                                        </div>

                                        @if($event->start->isToday())
                                            <span class="status-badge status-today">Hôm nay</span>
                                        @elseif($event->start->isTomorrow())
                                            <span class="status-badge status-tomorrow">Ngày mai</span>
                                        @else
                                            <span class="status-badge status-upcoming">Sắp tới</span>
                                        @endif
                                    </div>

                                    {{-- META ROW --}}
                                    <div class="seminar-meta">
                                        <div class="seminar-meta-item">
                                            <i class="fa-regular fa-clock"></i>
                                            <span>{{ $event->start->format('H:i') }} - {{ $event->end ? $event->end->format('H:i') : '...' }}</span>
                                        </div>
                                        <div class="seminar-meta-item">
                                            <i class="fa-regular fa-user"></i>
                                            <span>{{ $event->user ? $event->user->name : 'Đang cập nhật' }}</span>
                                        </div>
                                    </div>

                                    {{-- MÔ TẢ --}}
                                    <div class="seminar-desc">
                                        <strong>Mô tả:</strong>
                                        {{ $event->description
                                            ? Str::limit($event->description, 110)
                                            : 'Nội dung buổi seminar đang được cập nhật.' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-calendar text-center">
                                <img src="{{ asset('assets/images/empty-calendar.png') }}" alt="empty">
                                <div class="text-center mb-1 fw-semibold">Hiện không có sự kiện sắp tới nào.</div>
                                <div>Hãy quay lại sau hoặc theo dõi thông báo từ Bộ môn / Phòng Lab.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- PAST --}}
        <div class="card">
            <div class="card-header">
                <h4>
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Các sự kiện đã diễn ra
                </h4>

                <form action="{{ url()->current() }}" method="GET" class="filter-seminar" id="frm-filter-seminar">
                    <div class="form-group mb-0">
                        <select name="year" id="filter-select-year" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả các năm</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    Năm {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="seminar-section">
                    <div class="seminar-list">
                        @forelse($pastEvents as $event)
                            @php
                                $categoryClass = 'category-' . ($event->category ?? 'other');
                            @endphp
                            <div class="seminar-item" style="background: #f9fafb;">
                                <div class="date_stamp" style="background: #e5e7eb; color:#374151;">
                                    <div class="date_stamp_inner">
                                        <div class="weekday">
                                            {{ $event->start->isoFormat('dddd') }}
                                        </div>
                                        <div class="event_date">
                                            {{ $event->start->format('d') }}
                                        </div>
                                        <p class="date_time_bottom">
                                            {{ $event->start->format('m/Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="seminar_item_right">
                                    <div class="article-title">
                                        <div>
                                            <a href="#" style="color:#111827;">{{ $event->title }}</a>
                                            <div class="category-pill {{ $categoryClass }}">
                                                <span class="category-dot"></span>
                                                {{ ucfirst($event->category) }}
                                            </div>
                                        </div>
                                        <span class="status-badge status-done">Đã xong</span>
                                    </div>

                                    <div class="seminar-meta">
                                        <div class="seminar-meta-item">
                                            <i class="fa-regular fa-clock"></i>
                                            <span>{{ $event->start->format('H:i') }} - {{ $event->end ? $event->end->format('H:i') : '' }}</span>
                                        </div>
                                        <div class="seminar-meta-item">
                                            <i class="fa-regular fa-user"></i>
                                            <span>{{ $event->user ? $event->user->name : 'Đang cập nhật' }}</span>
                                        </div>
                                    </div>

                                    <div class="seminar-desc">
                                        <strong>Mô tả:</strong>
                                        {{ $event->description
                                            ? Str::limit($event->description, 110)
                                            : 'Không có mô tả chi tiết.' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-calendar text-center">
                                <img src="{{ asset('assets/images/empty-calendar.png') }}" alt="empty">
                                <div class="text-center mb-1 fw-semibold">Không tìm thấy sự kiện nào trong quá khứ.</div>
                                <div>Hãy thử chọn năm khác hoặc bỏ lọc.</div>
                            </div>
                        @endforelse

                        <div class="mt-3 px-3">
                            {{ $pastEvents->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-client-layout>
