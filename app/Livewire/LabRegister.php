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

    // MULTI FILE UPLOAD
    /** @var array<int,\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public $uploads = [];
    public $uploadIteration = 0;

    public $form = [
        'title'         => '',
        'category'      => 'work',
        'lab_code'      => '',
        'user_id'       => '',
        'group_id'      => '',
        'start'         => '',
        'end'           => '',
        'description'   => '',
        'status'        => 'approved',
        'color'         => '#2563eb',

        // LẶP LỊCH
        'repeat_type'   => '',        // '', daily, weekly, monthly
        'repeat_until'  => '',        // Y-m-d
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
            'form.title'       => 'required|string|max:255',
            'form.category'    => 'required|in:work,seminar,other',
            'form.lab_code'    => 'required|string',
            'form.user_id'     => 'required|exists:users,id',
            'form.group_id'    => 'nullable|exists:groups,id',
            'form.start'       => 'required|date',
            'form.end'         => 'required|date|after:form.start',
            'form.description' => 'nullable|string',
            'form.status'      => 'required|in:pending,approved,cancelled',
            'form.color'       => 'nullable|string|max:20',

            // Lặp
            'form.repeat_type'  => 'nullable|in:daily,weekly,monthly',
            'form.repeat_until' => 'nullable|date|after_or_equal:form.start',

            // Upload
            'uploads'   => 'nullable|array|max:8',
            'uploads.*' => 'file|max:10240',
        ];
    }

    public function removeUpload(int $index): void
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    // check trùng
    protected function hasConflict(string $labCode, Carbon $start, Carbon $end): bool
    {
        return LabEvent::where('status', 'approved')
            ->where('lab_code', $labCode)
            ->where(function ($q) use ($start, $end) {
                $q->where('start', '<', $end)
                  ->where('end', '>', $start);
            })
            ->exists();
    }

    public function createEvent()
    {
        $this->validate();

        $start = Carbon::parse($this->form['start']);
        $end   = Carbon::parse($this->form['end']);

        if (now()->greaterThan($start)) {
            $this->dispatch('toast', detail: [
                'type'    => 'error',
                'message' => 'Không thể tạo lịch trong quá khứ.',
                'sub'     => 'Vui lòng chọn thời gian bắt đầu từ hiện tại trở đi.',
            ]);
            return;
        }

        //lặp
        $occurrences = [];

        $repeatType  = $this->form['repeat_type'] ?? '';
        $repeatUntil = $this->form['repeat_until']
            ? Carbon::parse($this->form['repeat_until'])->endOfDay()
            : null;

        if (!$repeatType || !$repeatUntil) {
            // Không lặp
            $occurrences[] = [
                'start' => $start->copy(),
                'end'   => $end->copy(),
            ];
        } else {
            $curStart = $start->copy();
            $curEnd   = $end->copy();

            while ($curStart->lessThanOrEqualTo($repeatUntil)) {
                $occurrences[] = [
                    'start' => $curStart->copy(),
                    'end'   => $curEnd->copy(),
                ];

                if ($repeatType === 'daily') {
                    $curStart->addDay();
                    $curEnd->addDay();
                } elseif ($repeatType === 'weekly') {
                    $curStart->addWeek();
                    $curEnd->addWeek();
                } elseif ($repeatType === 'monthly') {
                    $curStart->addMonthNoOverflow();
                    $curEnd->addMonthNoOverflow();
                } else {
                    break;
                }
            }
        }

        // check trùng 
        if ($this->form['status'] === 'approved') {
            foreach ($occurrences as $occ) {
                if ($this->hasConflict($this->form['lab_code'], $occ['start'], $occ['end'])) {
                    $this->dispatch('open-conflict-modal');
                    $this->dispatch('toast', detail: [
                        'type'    => 'error',
                        'message' => 'Trùng khung giờ với lịch đã duyệt.',
                        'sub'     => 'Đổi thời gian / phòng hoặc chuyển trạng thái sang "Chờ duyệt".',
                    ]);
                    return;
                }
            }
        }

        // ====== TẠO EVENT TRONG DB ======
        $createdEvents = [];

        foreach ($occurrences as $index => $occ) {
            $event = LabEvent::create([
                'title'          => $this->form['title'],
                'category'       => $this->form['category'],
                'lab_code'       => $this->form['lab_code'],
                'user_id'        => $this->form['user_id'],
                'registered_for' => $this->form['group_id'] ?: null,
                'start'          => $occ['start'],
                'end'            => $occ['end'],
                'description'    => $this->form['description'],
                'status'         => $this->form['status'],
                'color'          => $this->form['color'] ?: null,
            ]);

            $createdEvents[] = $event;

            // Gắn file cho occurrence đầu tiên
            if ($index === 0 && !empty($this->uploads)) {
                foreach ($this->uploads as $file) {
                    if (!$file) {
                        continue;
                    }

                    $original = $file->getClientOriginalName();
                    $safeName = Str::random(6) . '_' . time() . '_' .
                        preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $original);

                    $path = $file->storeAs('lab-events/' . $event->id, $safeName, 'public');

                    LabEventFile::create([
                        'lab_event_id' => $event->id,
                        'file_name'    => $original,
                        'file_path'    => $path,
                    ]);
                }
            }
        }

         $this->form = [
            'title'         => '',
            'category'      => 'work',
            'lab_code'      => '',
            'user_id'       => '',
            'group_id'      => '',
            'start'         => '',
            'end'           => '',
            'description'   => '',
            'status'        => 'approved',
            'color'         => '#2563eb',
            'repeat_type'   => '',
            'repeat_until'  => '',
        ];
        
        $this->uploads = [];
        $this->uploadIteration++;

        $count = count($createdEvents);

        $this->dispatch('toast', detail: [
            'type'    => 'success',
            'message' => "Đã tạo {$count} lịch thành công.",
            'sub'     => $count > 1
                ? 'Đã sinh nhiều lịch theo thiết lập lặp.'
                : 'Form đã được làm mới.',
        ]);
    }
     
    public function render()
    {
        return view('livewire.lab-register')
            ->layout('components.layouts.admin-layout');
    }
}
