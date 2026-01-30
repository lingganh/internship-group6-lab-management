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
        'briefcase' => 'Briefcase',
        'clock' => 'Clock',
        'users' => 'Users',
        'calendar' => 'Calendar',
        'star' => 'Star',
        'bell' => 'Bell',
        'coffee' => 'Coffee',
        'home' => 'Home',
        'graduation-cap' => 'Graduation',
        'question-circle' => 'Question',
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

            $this->dispatch('toast', 
                type: 'success', 
                message: 'Cập nhật thành công', 
                description: "Đã cập nhật {$updatedCount} sự kiện"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('toast', 
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
        
        $this->dispatch('toast', 
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