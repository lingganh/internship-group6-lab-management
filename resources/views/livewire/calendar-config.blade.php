<div>
    {{-- resources/views/livewire/calendar-config.blade.php --}}

<div>
    <div class="page-header page-header-light shadow-sm">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">Cấu hình lịch sự kiện</h4>
            </div>
        </div>
        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                        <i class="ph-house"></i>
                    </a>
                    <span class="breadcrumb-item active">Cấu hình lịch</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <form wire:submit.prevent="saveSettings">
            
            {{-- Màu sắc theo TRẠNG THÁI - RESPONSIVE --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title fw-bold mb-1">🎨 Màu sắc theo trạng thái</h5>
                    <p class="text-muted small mb-3 mb-md-4">Mỗi trạng thái sẽ hiển thị với màu riêng trên lịch</p>
                    
                    {{-- Responsive Grid: 1 col mobile, 2 cols tablet, 4 cols desktop --}}
                    <div class="row g-2 g-md-3">
                        @foreach($statusColors as $code => $status)
                            <div class="col-12 col-sm-6 col-lg-3" wire:key="status-{{ $code }}">
                                <div class="color-card p-3 p-md-4 rounded-4 h-100" style="background: linear-gradient(135deg, {{ $status['color'] }}15 0%, {{ $status['color'] }}05 100%); border: 2px solid {{ $status['color'] }}30;">
                                    <h6 class="fw-semibold mb-2 mb-md-3 text-center">{{ $status['name'] }}</h6>
                                    
                                    <div class="d-flex flex-row align-items-center justify-content-center gap-2 p-2 bg-white rounded-4 border shadow-sm mx-auto" style="max-width: fit-content;">
                                        <div style="width: 45px; height: 25px; overflow: hidden; border-radius: 8px;" class="border">
                                            <input 
                                                type="color" 
                                                wire:model.live="statusColors.{{ $code }}.color"
                                                class="form-control form-control-color border-0 p-0"
                                            />
                                        </div>

                                        <input 
                                            type="text" 
                                            wire:model.blur="statusColors.{{ $code }}.color"
                                            class="form-control border-0 bg-light text-center fw-bold"
                                            placeholder="#000000"
                                            maxlength="7"
                                            style="width: 90px; height: 36px; font-family: 'Inter', monospace; font-size: 11px; border-radius: 8px; color: #4b5563;"
                                        />
                                    </div>
 
                                    @error("statusColors.{$code}.color")
                                        <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Icon theo LOẠI SỰ KIỆN - RESPONSIVE --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title fw-bold mb-1">📋 Icon theo loại sự kiện</h5>
                    <p class="text-muted small mb-3 mb-md-4">Icon sẽ hiển thị trên mỗi sự kiện để phân biệt loại</p>
                    
                    {{-- Responsive Grid: 1 col mobile, 2 cols tablet, 3 cols desktop --}}
                    <div class="row g-2 g-md-3">
                        @foreach($categoryIcons as $code => $category)
                            <div class="col-12 col-md-6 col-lg-4" wire:key="category-{{ $code }}">
                                <div class="icon-card p-3 p-md-4 rounded-4 h-100 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-2 mb-md-3">
                                        <h6 class="fw-semibold mb-0 small">{{ $category['name'] }}</h6>
                                        <span class="badge bg-primary small">{{ strtoupper($code) }}</span>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 align-items-center">
                                        <select 
                                            wire:model.live="categoryIcons.{{ $code }}.icon"
                                            class="form-select form-select-sm rounded-3 w-100"
                                        >
                                            @foreach($iconOptions as $iconValue => $iconLabel)
                                                <option value="{{ $iconValue }}">{{ $iconLabel }}</option>
                                            @endforeach
                                        </select>
                                        <div class="icon-preview text-center mt-2 mt-sm-0" style="min-width: 45px;">
                                            <i class="ph ph-{{ $category['icon'] }}" style="font-size: 28px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LỊCH PREVIEW - RESPONSIVE --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                        <div>
                            <h5 class="card-title fw-bold mb-1">📅 Xem trước trên lịch</h5>
                            <p class="text-muted small mb-0">Hiển thị tất cả kết hợp loại sự kiện + trạng thái</p>
                        </div>
                    </div>

                    {{-- Calendar Preview --}}
                    <div class="calendar-preview">
                        <div class="calendar-header text-center mb-3">
                            <h4 class="fw-bold mb-1 h5 h4-md">Tháng 1 năm 2026</h4>
                        </div>

                        {{-- Week Header - Desktop --}}
                        <div class="row g-0 border-bottom mb-2 d-none d-lg-flex">
                            @foreach(['Thứ 2 26/1', 'Thứ 3 27/1', 'Thứ 4 28/1', 'Thứ 5 29/1', 'Thứ 6 30/1', 'Thứ 7 31/1', 'CN 1/2'] as $day)
                                <div class="col text-center py-2 fw-semibold" style="font-size: 13px;">
                                    {{ $day }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Week Header - Mobile/Tablet (Abbreviated) --}}
                        <div class="row g-0 border-bottom mb-2 d-flex d-lg-none">
                            @foreach(['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'] as $day)
                                <div class="col text-center py-2 fw-semibold small">
                                    {{ $day }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Calendar Grid --}}
                        <div class="calendar-grid">
                            @php
                                $sampleEvents = [
                                    ['day' => 1, 'time' => '13:30 - 15:00', 'title' => 'Event test #1', 'category' => 'work', 'status' => 'pending'],
                                    ['day' => 2, 'time' => '15:00 - 16:30', 'title' => 'Event test #2', 'category' => 'seminar', 'status' => 'approved'],
                                    ['day' => 3, 'time' => '13:30 - 15:00', 'title' => 'Event test #3', 'category' => 'other', 'status' => 'cancelled'],
                                    ['day' => 4, 'time' => '13:30 - 15:00', 'title' => 'Event test #4', 'category' => 'work', 'status' => 'completed'],
                                    ['day' => 5, 'time' => '13:30 - 15:00', 'title' => 'Event test #5', 'category' => 'seminar', 'status' => 'pending'],
                                    ['day' => 6, 'time' => '9:00 - 10:30', 'title' => 'Event test #6', 'category' => 'work', 'status' => 'approved'],
                                ];
                            @endphp

                            {{-- Desktop View --}}
                            <div class="row g-2 d-none d-lg-flex">
                                @for($day = 0; $day < 7; $day++)
                                    <div class="col">
                                        <div class="calendar-day-cell border rounded-3 p-2 bg-light" style="min-height: 400px;">
                                            @foreach($sampleEvents as $event)
                                                @if($event['day'] == $day)
                                                    @php
                                                        $statusColor = $statusColors[$event['status']]['color'] ?? '#cccccc';
                                                        $categoryIcon = $categoryIcons[$event['category']]['icon'] ?? 'calendar';
                                                    @endphp
                                                    
                                                    <div class="calendar-event-card rounded-3 p-2 mb-2 shadow-sm" 
                                                         style="background-color: {{ $statusColor }}; cursor: pointer;"
                                                         wire:key="event-desktop-{{ $event['day'] }}-{{ $event['title'] }}">
                                                        
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <small class="text-white fw-bold" style="font-size: 11px;">
                                                                {{ $event['time'] }}
                                                            </small>
                                                        </div>

                                                        <div class="d-flex align-items-start gap-1">
                                                            <i class="ph ph-{{ $categoryIcon }} text-white" style="font-size: 14px;"></i>
                                                            <div class="flex-grow-1">
                                                                <div class="text-white fw-bold" style="font-size: 13px; line-height: 1.2;">
                                                                    {{ $event['title'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Mobile/Tablet Horizontal Scroll View --}}
                            <div class="d-lg-none">
                                <div class="calendar-scroll-wrapper">
                                    <div class="d-flex gap-2 pb-2" style="overflow-x: auto; scroll-snap-type: x mandatory;">
                                        @for($day = 0; $day < 7; $day++)
                                            <div class="calendar-day-mobile" style="min-width: 160px; scroll-snap-align: start;">
                                                <div class="calendar-day-cell border rounded-3 p-2 bg-light" style="min-height: 300px;">
                                                    @foreach($sampleEvents as $event)
                                                        @if($event['day'] == $day)
                                                            @php
                                                                $statusColor = $statusColors[$event['status']]['color'] ?? '#cccccc';
                                                                $categoryIcon = $categoryIcons[$event['category']]['icon'] ?? 'calendar';
                                                            @endphp
                                                            
                                                            <div class="calendar-event-card rounded-3 p-2 mb-2 shadow-sm" 
                                                                 style="background-color: {{ $statusColor }}; cursor: pointer;"
                                                                 wire:key="event-mobile-{{ $event['day'] }}-{{ $event['title'] }}">
                                                                
                                                                <div class="mb-1">
                                                                    <small class="text-white fw-bold" style="font-size: 10px;">
                                                                        {{ $event['time'] }}
                                                                    </small>
                                                                </div>

                                                                <div class="d-flex align-items-start gap-1">
                                                                    <i class="ph ph-{{ $categoryIcon }} text-white" style="font-size: 12px;"></i>
                                                                    <div class="flex-grow-1">
                                                                        <div class="text-white fw-bold" style="font-size: 11px; line-height: 1.2;">
                                                                            {{ $event['title'] }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        <i class="ph ph-swipe-right"></i> Vuốt để xem các ngày khác
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons - Responsive --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button wire:loading.remove wire:target="saveSettings" wire:click="saveSettings" class="btn btn-primary text-nowrap">
                    <i class="ph-floppy-disk"></i> Lưu
                </button>

                <button wire:loading wire:target="saveSettings" class="btn btn-primary text-nowrap" disabled>
                    <i class="ph-spinner-gap animate-spin"></i> Đang lưu...
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Base Styles */
.color-card, .icon-card {
    transition: all 0.3s ease;
}

.color-card:hover, .icon-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.form-control-color {
    cursor: pointer;
}

/* Calendar Styles */
.calendar-preview {
    background: white;
    border-radius: 12px;
}

.calendar-day-cell {
    background: #f8f9fa !important;
    transition: background 0.2s;
}

.calendar-day-cell:hover {
    background: #e9ecef !important;
}

.calendar-event-card {
    transition: all 0.2s ease;
}

.calendar-event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.25) !important;
}

/* Mobile Calendar Scroll */
.calendar-scroll-wrapper {
    -webkit-overflow-scrolling: touch;
}

.calendar-scroll-wrapper::-webkit-scrollbar {
    height: 8px;
}

.calendar-scroll-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.calendar-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.calendar-scroll-wrapper::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive Typography */
@media (max-width: 576px) {
    .page-title {
        font-size: 1.25rem !important;
    }
    
    .card-title {
        font-size: 1.1rem !important;
    }
    
    .h4-md {
        font-size: 1.25rem;
    }
}

/* Tablet Specific */
@media (min-width: 577px) and (max-width: 991px) {
    .calendar-day-mobile {
        min-width: 200px !important;
    }
    
    .calendar-day-cell {
        min-height: 350px !important;
    }
}

/* Desktop */
@media (min-width: 992px) {
    .h4-md {
        font-size: 1.5rem;
    }
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {
    .color-card:hover, .icon-card:hover {
        transform: none;
    }
    
    .color-card:active, .icon-card:active {
        transform: scale(0.98);
    }
    
    .calendar-event-card:hover {
        transform: none;
    }
    
    .calendar-event-card:active {
        transform: scale(0.97);
    }
}

/* Landscape Mobile */
@media (max-width: 991px) and (orientation: landscape) {
    .calendar-day-cell {
        min-height: 250px !important;
    }
}

/* Small Mobile Devices */
@media (max-width: 375px) {
    .color-card, .icon-card {
        font-size: 0.9rem;
    }
    
    .calendar-day-mobile {
        min-width: 140px !important;
    }
    
    .calendar-event-card {
        font-size: 0.85rem !important;
    }
}

/* Print Styles */
@media print {
    .btn, .breadcrumb {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>
</div>