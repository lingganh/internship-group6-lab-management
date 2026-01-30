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
            
            {{-- Màu sắc theo TRẠNG THÁI - 1 HÀNG --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-1">  Màu sắc theo trạng thái</h5>
                    <p class="text-muted small mb-4">Mỗi trạng thái sẽ hiển thị với màu riêng trên lịch</p>
                    
                    {{-- 1 HÀNG với 4 cột --}}
                    <div class="row g-3">
                        @foreach($statusColors as $code => $status)
                            <div class="col-md-3" wire:key="status-{{ $code }}">
                                <div class="color-card p-4 rounded-4 h-100 justify-content-center" style="background: linear-gradient(135deg, {{ $status['color'] }}15 0%, {{ $status['color'] }}05 100%); border: 2px solid {{ $status['color'] }}30;">
                                    <h6 class="fw-semibold mb-3 text-center">{{ $status['name'] }}</h6>
                                    
                                    <div class="d-flex flex-row align-items-center justify-content-center gap-2 p-2 bg-white rounded-4 border shadow-sm" style="width: fit-content;">
                                    <div style="width: 50px; height: 25px; overflow: hidden; border-radius: 8px;" class="border">
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
                                        style="width: 95px; height: 38px; font-family: 'Inter', monospace; font-size: 12px; border-radius: 8px; color: #4b5563;"
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

            {{-- Icon theo LOẠI SỰ KIỆN --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-1">  Icon theo loại sự kiện</h5>
                    <p class="text-muted small mb-4">Icon sẽ hiển thị trên mỗi sự kiện để phân biệt loại</p>
                    
                    <div class="row g-3">
                        @foreach($categoryIcons as $code => $category)
                            <div class="col-md-4" wire:key="category-{{ $code }}">
                                <div class="icon-card p-4 rounded-4 h-100 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0">{{ $category['name'] }}</h6>
                                        <span class="badge bg-primary">{{ strtoupper($code) }}</span>
                                    </div>
                                    
                                    <div class="d-flex gap-3 align-items-center">
                                        <select 
                                            wire:model.live="categoryIcons.{{ $code }}.icon"
                                            class="form-select rounded-3"
                                        >
                                            @foreach($iconOptions as $iconValue => $iconLabel)
                                                <option value="{{ $iconValue }}">{{ $iconLabel }}</option>
                                            @endforeach
                                        </select>
                                        <div class="icon-preview text-center" style="min-width: 50px;">
                                            <i class="ph ph-{{ $category['icon'] }}" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LỊCH PREVIEW --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title fw-bold mb-1"> Xem trước trên lịch</h5>
                            <p class="text-muted small mb-0">Hiển thị tất cả kết hợp loại sự kiện + trạng thái</p>
                        </div>
                        {{-- <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-light rounded-pill">
                                <i class="ph ph-caret-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3">
                                Hôm nay
                            </button>
                            <button type="button" class="btn btn-sm btn-light rounded-pill">
                                <i class="ph ph-caret-right"></i>
                            </button>
                        </div> --}}
                    </div>

                    {{-- Calendar Header --}}
                    <div class="calendar-preview">
                        <div class="calendar-header text-center mb-3">
                            <h4 class="fw-bold mb-1">Tháng 1 năm 2026</h4>
                            {{-- <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary">Tháng</button>
                                <button type="button" class="btn btn-primary">Tuần</button>
                                <button type="button" class="btn btn-outline-secondary">Ngày</button>
                            </div> --}}
                        </div>

                        {{-- Week Header --}}
                        <div class="row g-0 border-bottom mb-2">
                            @foreach(['Thứ 2 26/1', 'Thứ 3 27/1', 'Thứ 4 28/1', 'Thứ 5 29/1', 'Thứ 6 30/1', 'Thứ 7 31/1', 'CN 1/2'] as $day)
                                <div class="col text-center py-2 fw-semibold" style="font-size: 13px;">
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
                                    ['day' => 4, 'time' => '13:30 - 15:00', 'title' => 'Event test #4', 'category' => 'work', 'status' => 'rejected'],
                                    ['day' => 5, 'time' => '13:30 - 15:00', 'title' => 'Event test #5', 'category' => 'seminar', 'status' => 'pending'],
                                    ['day' => 6, 'time' => '9:00 - 10:30', 'title' => 'Event test #6', 'category' => 'work', 'status' => 'approved'],
                                ];
                            @endphp

                            <div class="row g-2">
                                @for($day = 0; $day < 7; $day++)
                                    <div class="col">
                                        <div class="calendar-day-cell border rounded-3 p-2 bg-light" style="min-height: 400px;">
                                            {{-- Events in this day --}}
                                            @foreach($sampleEvents as $event)
                                                @if($event['day'] == $day)
                                                    @php
                                                        $statusColor = $statusColors[$event['status']]['color'] ?? '#cccccc';
                                                        $categoryIcon = $categoryIcons[$event['category']]['icon'] ?? 'calendar';
                                                    @endphp
                                                    
                                                    <div class="calendar-event-card rounded-3 p-2 mb-2 shadow-sm" 
                                                         style="background-color: {{ $statusColor }}; cursor: pointer;"
                                                         wire:key="event-{{ $event['day'] }}-{{ $event['title'] }}">
                                                        
                                                        {{-- Check icon --}}
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <small class="text-white fw-bold" style="font-size: 11px;">
                                                                {{ $event['time'] }}
                                                            </small>
                                                            {{-- <div class="bg-white bg-opacity-25 rounded-circle" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="ph ph-check text-white" style="font-size: 12px;"></i>
                                                            </div> --}}
                                                        </div>

                                                        {{-- Title + Icon --}}
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
             <div class="d-flex align-items-center gap-2 flex-nowrap">
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


 
/* Responsive */
@media (max-width: 768px) {
    .calendar-grid .col {
        min-width: 150px;
    }
    
    .row.g-2 {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
}
</style>
</div>