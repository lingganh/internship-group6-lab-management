<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Lab;
use App\Models\User;
use App\Models\LabEvent;
use App\Models\LabEventFile;
use Illuminate\Support\Str;

class LabRegister extends Component
{
    use WithFileUploads;

    public $labs = [];
    public $users = [];
    public $isAdmin = false;

    public $uploads = []; // array file upload

    public $form = [
        'title' => '',
        'category' => 'work',
        'lab_code' => '',
        'user_id' => '',
        'start' => '',
        'end' => '',
        'description' => '',
        'status' => 'approved',
        'color' => '#2563eb',
    ];

    public function mount()
    {
        // $this->isAdmin = (bool) (auth()->user()?->is_admin ?? false);
        // if (!$this->isAdmin) abort(403);

        $this->labs = Lab::select('code', 'name')->orderBy('name')->get();
        $this->users = User::select('id', 'full_name', 'email')->orderBy('full_name')->get();
    }

    protected function rules()
    {
        return [
            'form.title' => 'required|string|max:255',
            'form.category' => 'required|in:work,seminar,other',
            'form.lab_code' => 'required|string',
            'form.user_id' => 'required|exists:users,id',
            'form.start' => 'required|date',
            'form.end' => 'required|date|after:form.start',
            'form.description' => 'nullable|string',
            'form.status' => 'required|in:pending,approved,cancelled',
            'form.color' => 'nullable|string|max:20',

            // upload
            'uploads' => 'nullable|array|max:8',
            'uploads.*' => 'file|max:10240', // 10MB mỗi file
        ];
    }

    public function updatedUploads()
    {
        // validate ngay khi chọn file
        $this->validateOnly('uploads');
    }

    public function removeUpload($index)
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function createEvent()
    {
        $this->validate();

        // CHẶN TẠO TRONG QUÁ KHỨ  
        if (now()->greaterThan(\Carbon\Carbon::parse($this->form['start']))) {
            $this->dispatch('toast', detail: [
                'type' => 'error',
                'message' => 'Không thể tạo lịch trong quá khứ.',
                'sub' => 'Vui lòng chọn thời gian bắt đầu từ hiện tại trở đi.'
            ]);
            return;
        }

        // check trùng lịch theo phòng nếu status=approved
        if ($this->form['status'] === 'approved') {
            $hasConflict = LabEvent::where('status', 'approved')
                ->where('lab_code', $this->form['lab_code'])
                ->where(function ($q) {
                    $q->where('start', '<', $this->form['end'])
                      ->where('end', '>', $this->form['start']);
                })
                ->exists();

            if ($hasConflict) {
                $this->dispatch('toast', detail: [
                    'type' => 'error',
                    'message' => 'Trùng khung giờ với lịch đã duyệt.',
                    'sub' => 'Đổi thời gian hoặc chuyển trạng thái sang “Chờ duyệt”.'
                ]);
                return;
            }
        }

        $event = LabEvent::create([
            'title' => $this->form['title'],
            'category' => $this->form['category'],
            'lab_code' => $this->form['lab_code'],
            'user_id' => $this->form['user_id'],
            'start' => $this->form['start'],
            'end' => $this->form['end'],
            'description' => $this->form['description'],
            'status' => $this->form['status'],
            'color' => $this->form['color'] ?: null,
        ]);

         if (!empty($this->uploads)) {
            foreach ($this->uploads as $file) {
                if (!$file) continue;

                $original = $file->getClientOriginalName();
                $safeName = Str::random(6) . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $original);

                 $path = $file->storeAs('lab-events/' . $event->id, $safeName, 'public');

                LabEventFile::create([
                    'lab_event_id' => $event->id,
                    'file_name' => $original,
                    'file_path' => $path,
                ]);
            }
        }

        $this->reset('uploads');
        $this->reset('form');
        $this->form['category'] = 'work';
        $this->form['status'] = 'approved';
        $this->form['color'] = '#2563eb';

        $this->dispatch('toast', detail: [
            'type' => 'success',
            'message' => 'Đã tạo lịch thành công.',
            'sub' => 'Lịch và file đính kèm đã được lưu.'
        ]);
    }

    public function render()
    {
        return view('livewire.lab-register')->layout('components.layouts.admin-layout');
    }
}
