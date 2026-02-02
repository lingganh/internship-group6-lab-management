<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EventCategory;
use App\Models\EventStatus;
use App\Models\LabEvent;
use Illuminate\Support\Facades\DB;

class CalendarConfig extends Component
{
    public $statusColors = [];
    public $categoryIcons = [];
    public $previewEvents = []; // Thêm preview

    public $iconOptions = [
         'briefcase' => 'Cặp công việc',
        'building' => 'Tòa nhà',
        'clipboard' => 'Clipboard',
        'file-text' => 'Tài liệu',
        'folder' => 'Thư mục',
        'pen' => 'Bút viết',
        'paperclip' => 'Kẹp giấy',
        'archive' => 'Lưu trữ',

         'graduation-cap' => 'Tốt nghiệp',
        'book' => 'Sách',
        'book-open' => 'Sách mở',
        'chalkboard' => 'Bảng đen',
        'user-graduate' => 'Sinh viên',
        'pencil' => 'Bút chì',
        'highlighter' => 'Bút dạ quang',

         'users' => 'Nhóm người',
        'user-group' => 'Nhóm',
        'people' => 'Mọi người',
        'handshake' => 'Bắt tay',
        'comments' => 'Thảo luận',
        'video' => 'Video call',

         'calendar' => 'Lịch',
        'calendar-days' => 'Lịch ngày',
        'clock' => 'Đồng hồ',
        'hourglass' => 'Đồng hồ cát',
        'stopwatch' => 'Bấm giờ',
        'alarm-clock' => 'Báo thức',

         'flask' => 'Bình thí nghiệm',
        'microscope' => 'Kính hiển vi',
        'atom' => 'Nguyên tử',
        'laptop' => 'Laptop',
        'desktop' => 'Máy tính',
        'code' => 'Lập trình',
        'wifi' => 'Wifi',
        'server' => 'Máy chủ',

         'star' => 'Ngôi sao',
        'trophy' => 'Cúp',
        'medal' => 'Huy chương',
        'award' => 'Giải thưởng',
        'target' => 'Mục tiêu',
        'flag' => 'Cờ',

         'bell' => 'Chuông',
        'bell-ring' => 'Chuông rung',
        'megaphone' => 'Loa phát thanh',
        'bullhorn' => 'Loa',
        'envelope' => 'Thư',
        'message' => 'Tin nhắn',

         'home' => 'Nhà',
        'door-open' => 'Cửa mở',
        'landmark' => 'Địa điểm',
        'map-pin' => 'Vị trí',
        'map' => 'Bản đồ',

         'coffee' => 'Cà phê',
        'mug-hot' => 'Cốc nóng',
        'pizza-slice' => 'Pizza',
        'utensils' => 'Dao nĩa',
        'cake' => 'Bánh',

         'palette' => 'Bảng màu',
        'paint-brush' => 'Cọ vẽ',
        'camera' => 'Máy ảnh',
        'image' => 'Hình ảnh',
        'music' => 'Âm nhạc',

         'check' => 'Đúng',
        'check-circle' => 'Hoàn thành',
        'x-circle' => 'Hủy',
        'question-circle' => 'Câu hỏi',
        'exclamation-triangle' => 'Cảnh báo',
        'info-circle' => 'Thông tin',

         'wrench' => 'Cờ lê',
        'screwdriver' => 'Tua vít',
        'hammer' => 'Búa',
        'tools' => 'Công cụ',
        'cog' => 'Bánh răng',
        'sliders' => 'Điều chỉnh',

         'rocket' => 'Tên lửa',
        'lightbulb' => 'Bóng đèn',
        'puzzle-piece' => 'Mảnh ghép',
        'chart-line' => 'Biểu đồ',
        'chart-bar' => 'Cột biểu đồ',
        'database' => 'Cơ sở dữ liệu',
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->generatePreviewEvents();
    }

    public function loadSettings()
    {
        $statuses = EventStatus::where('is_active', true)->get();
        foreach ($statuses as $status) {
            $this->statusColors[$status->code] = [
                'name' => $status->name,
                'color' => $status->color
            ];
        }

        $categories = EventCategory::where('is_active', true)->get();
        foreach ($categories as $category) {
            $this->categoryIcons[$category->code] = [
                'name' => $category->name,
                'icon' => $category->icon
            ];
        }
    }

    public function generatePreviewEvents()
    {
        $this->previewEvents = [];

        foreach ($this->categoryIcons as $catCode => $category) {
            foreach ($this->statusColors as $statusCode => $status) {
                $this->previewEvents[] = [
                    'category' => $catCode,
                    'category_name' => $category['name'],
                    'category_icon' => $category['icon'],
                    'status' => $statusCode,
                    'status_name' => $status['name'],
                    'status_color' => $status['color'],
                    'title' => 'Họp nhóm dự án',
                    'time' => '13:30 - 15:00',
                    'location' => 'Làm việc - nghiên cứu'
                ];
            }
        }
    }

    public function updatedStatusColors()
    {
        $this->generatePreviewEvents();
    }

    public function updatedCategoryIcons()
    {
        $this->generatePreviewEvents();
    }

    public function saveSettings()
    {
        $this->validate([
            'statusColors.*.color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'categoryIcons.*.icon' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($this->statusColors as $code => $data) {
                EventStatus::where('code', $code)->update(['color' => $data['color']]);
            }

            foreach ($this->categoryIcons as $code => $data) {
                EventCategory::where('code', $code)->update(['icon' => $data['icon']]);
            }

            $updatedCount = 0;
            foreach ($this->statusColors as $code => $data) {
                $count = LabEvent::where('status', $code)
                    ->update(['color' => $data['color']]);
                $updatedCount += $count;
            }

            DB::commit();

            $this->dispatch('alert',
                type: 'success',
                message: 'Cập nhật thành công',
                description: "Đã cập nhật {$updatedCount} sự kiện"
            );

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('alert',
                type: 'error',
                message: 'Cập nhật thất bại',
                description: $e->getMessage()
            );
        }
    }

    public function resetToDefault()
    {
        EventStatus::where('code', 'pending')->update(['color' => '#ffa500']);
        EventStatus::where('code', 'approved')->update(['color' => '#00ff00']);
        EventStatus::where('code', 'cancelled')->update(['color' => '#ff0000']);

        EventCategory::where('code', 'work')->update(['icon' => 'briefcase']);
        EventCategory::where('code', 'seminar')->update(['icon' => 'clock']);
        EventCategory::where('code', 'other')->update(['icon' => 'question-circle']);

        $this->loadSettings();
        $this->generatePreviewEvents();

        $this->dispatch('alert',
            type: 'info',
            message: 'Đã reset về mặc định',
            description: 'Nhấn "Lưu cấu hình" để áp dụng'
        );
    }

    public function render()
    {
        return view('livewire.calendar-config')
            ->layout('components.layouts.admin-layout');
    }
}
