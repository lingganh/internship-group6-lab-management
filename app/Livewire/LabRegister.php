<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Lab;
use App\Models\User;
use App\Models\Group;
use App\Models\LabEvent;
use App\Models\LabEventFile;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LabRegister extends Component
{
    use WithFileUploads;

    public $labs = [];
    public $users = [];
    public $groups = [];

    public $uploads = [];
    public $uploadIteration = 0;

    public $form = [
        'title' => '',
        'category' => 'work',
        'lab_code' => '',
        'user_id' => '',
        'group_id' => '',
        'start' => '',
        'end' => '',
        'description' => '',
        'status' => 'approved',
        'color' => '#2563eb',
    ];

    public function mount()
    {
        $this->labs = Lab::select('code', 'name')->orderBy('name')->get();
        $this->users = User::select('id', 'full_name', 'email')->orderBy('full_name')->get();
        $this->groups = Group::select('id', 'name')->orderBy('name')->get();
    }

    protected function rules()
    {
        return [
            'form.title' => 'required|string|max:255',
            'form.category' => 'required|in:work,seminar,other',
            'form.lab_code' => 'required|string',
            'form.user_id' => 'required|exists:users,id',
            'form.group_id' => 'nullable|exists:groups,id',
            'form.start' => 'required|date',
            'form.end' => 'required|date|after:form.start',
            'form.description' => 'nullable|string',
            'form.status' => 'required|in:pending,approved,cancelled',
            'form.color' => 'nullable|string|max:20',
            'uploads' => 'nullable|array|max:8',
            'uploads.*' => 'file|max:10240',
        ];
    }

    public function updatedUploads()
    {
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

        if (now()->greaterThan(Carbon::parse($this->form['start']))) {
            $this->dispatch('toast', detail: [
                'type' => 'error',
                'message' => 'Không thể tạo lịch trong quá khứ.',
                'sub' => 'Vui lòng chọn thời gian bắt đầu từ hiện tại trở đi.'
            ]);
            return;
        }

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
            'title'         => $this->form['title'],
            'category'      => $this->form['category'],
            'lab_code'      => $this->form['lab_code'],
            'user_id'       => $this->form['user_id'],
            'registered_for' => $this->form['group_id'] ?: null,
            'start'         => $this->form['start'],
            'end'           => $this->form['end'],
            'description'   => $this->form['description'],
            'status'        => $this->form['status'],
            'color'         => $this->form['color'] ?: null,
        ]);

        if (!empty($this->uploads)) {
            foreach ($this->uploads as $file) {
                if (!$file) {
                    continue;
                }

                $original = $file->getClientOriginalName();
                $safeName = Str::random(6) . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $original);

                $path = $file->storeAs('lab-events/' . $event->id, $safeName, 'public');

                LabEventFile::create([
                    'lab_event_id' => $event->id,
                    'file_name'    => $original,
                    'file_path'    => $path,
                ]);
            }
        }

        $this->form = [
            'title' => '',
            'category' => 'work',
            'lab_code' => '',
            'user_id' => '',
            'group_id' => '',
            'start' => '',
            'end' => '',
            'description' => '',
            'status' => 'approved',
            'color' => '#2563eb',
        ];

        $this->uploads = [];
        $this->uploadIteration++;

        $this->dispatch('toast', detail: [
            'type' => 'success',
            'message' => 'Đã tạo lịch thành công.',
            'sub' => 'Form đã được làm mới.'
        ]);
    }

    public function render()
    {
        return view('livewire.lab-register')
            ->layout('components.layouts.admin-layout');
    }
}
