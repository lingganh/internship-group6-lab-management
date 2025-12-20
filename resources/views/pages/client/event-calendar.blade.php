<x-client-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --text-dark: #0f172a;
            --text-gray: #64748b;

            --bg-card: #ffffff;
            --border-color: #e2e8f0;

            --st-today-bg: #fee2e2;
            --st-today-text: #991b1b;
            --st-tom-bg: #ffedd5;
            --st-tom-text: #9a3412;
            --st-up-bg: #dcfce7;
            --st-up-text: #166534;
            --st-done-bg: #f1f5f9;
            --st-done-text: #475569;

            --radius: 12px;
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-hover: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .seminar-container {
            width: 100%;
            padding: 30px;
            background-color: var(--bg-body);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }


        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--primary);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 24px;
        }


        .event-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            padding: 24px;
            display: flex;
            gap: 20px;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .date-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
            border-radius: 12px;
            height: 80px;
            width: 80px;
            min-width: 80px;
            text-align: center;
        }

        .date-day {
            font-size: 28px;
            font-weight: 900;
            line-height: 1;
            color: var(--primary);
        }

        .date-month {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-gray);
            margin-top: 4px;
        }

        .past-event {
            opacity: 0.85;
        }

        .past-event:hover {
            opacity: 1;
        }

        .past-event .date-day {
            color: var(--text-gray);
        }

        .event-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
        }

        .event-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-dark);
            text-decoration: none;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }

        .event-title:hover {
            color: var(--primary);
        }


        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-today {
            background: var(--st-today-bg);
            color: var(--st-today-text);
        }

        .badge-tomorrow {
            background: var(--st-tom-bg);
            color: var(--st-tom-text);
        }

        .badge-upcoming {
            background: var(--st-up-bg);
            color: var(--st-up-text);
        }

        .badge-done {
            background: var(--st-done-bg);
            color: var(--st-done-text);
        }

        .category-tag {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-gray);
            border: 1px solid var(--border-color);
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 4px;
        }

        /* META */
        .event-meta {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: var(--text-gray);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .event-desc {
            font-size: 14px;
            color: #4b5563;
            margin: 8px 0 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* FILTER */
        .custom-select {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            font-size: 14px;
            outline: none;
        }


        @media (max-width: 640px) {
            .seminar-container {
                padding: 16px;
            }

            .event-grid {
                grid-template-columns: 1fr;
            }

            .event-card {
                padding: 16px;
            }
        }
    </style>

    <div class="seminar-container">

        {{-- === UPCOMING EVENTS === --}}
        <div class="mb-5">
            <div class="section-header">
                <div class="section-title">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Sắp diễn ra</span>
                </div>
            </div>

            <div class="event-grid">
                @forelse($upcomingEvents as $event)
                    <div class="event-card">
                        <div class="date-box">
                            <span class="date-day">{{ $event->start->format('d') }}</span>
                            <span class="date-month">Tháng {{ $event->start->format('m') }}</span>
                        </div>
                        <div class="event-content">
                            <div class="event-header">
                                <div>
                                    <a href="#" class="event-title">{{ $event->title }}</a>
                                    <span class="category-tag">{{ ucfirst($event->category ?? 'General') }}</span>
                                </div>
                                @if($event->start->isToday())
                                    <span class="badge badge-today">Hôm nay</span>
                                @elseif($event->start->isTomorrow())
                                    <span class="badge badge-tomorrow">Ngày mai</span>
                                @else
                                    <span class="badge badge-upcoming">Sắp tới</span>
                                @endif
                            </div>

                            <div class="event-desc">
                                {{ $event->description ? Str::limit($event->description, 100) : 'Nội dung đang cập nhật.' }}
                            </div>

                            <div class="event-meta">
                                <div class="meta-item">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $event->start->format('H:i') }}
                                </div>
                                <div class="meta-item">
                                    <i class="fa-regular fa-user"></i>
                                    {{ $event->user ? Str::limit($event->user->name, 15) : 'Unknown' }}
                                </div>
                                <div class="meta-item ms-auto">
                                    <i class="fa-solid fa-calendar-day"></i>
                                    {{ $event->start->isoFormat('dddd') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-5 text-muted">
                        Hiện không có sự kiện sắp tới.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- === PAST EVENTS === --}}
        {{-- <div>
            <div class="section-header">
                <div class="section-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Đã diễn ra</span>
                </div>
                <form action="{{ url()->current() }}" method="GET">
                    <select name="year" class="custom-select" onchange="this.form.submit()">
                        <option value="">Tất cả các năm</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>
                            Năm {{ $y }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="event-grid">
                @forelse($pastEvents as $event)
                <div class="event-card past-event" style="background: #f9fafb;">
                    <div class="date-box" style="background: #e2e8f0;">
                        <span class="date-day">{{ $event->start->format('d') }}</span>
                        <span class="date-month">Tháng {{ $event->start->format('m') }}</span>
                    </div>
                    <div class="event-content">
                        <div class="event-header">
                            <div>
                                <a href="#" class="event-title" style="color: #4b5563;">{{ $event->title }}</a>
                                <span class="category-tag">{{ ucfirst($event->category ?? 'General') }}</span>
                            </div>
                            <span class="badge badge-done">Đã xong</span>
                        </div>

                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fa-regular fa-clock"></i>
                                {{ $event->start->format('H:i') }}
                            </div>
                            <div class="meta-item">
                                <i class="fa-regular fa-user"></i>
                                {{ $event->user ? Str::limit($event->user->name, 15) : 'Unknown' }}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-5 text-muted">
                    Không tìm thấy sự kiện cũ.
                </div>
                @endforelse
            </div>
        </div> --}}

    </div>
</x-client-layout>