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

    // Dữ liệu dùng cho select
    public $labs = [];
    public $users = [];
    public $groups = [];

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

        // lặp
        'repeat_type'   => '',        // '', daily, weekly, monthly
        'repeat_until'  => '',        // Y-m-d
    ];

    // chọn trong 1 ngày
    public $eventDate = '';   // Y-m-d
    public $startTime = '';   // H:i
    public $endTime   = '';   // H:i
    public $totalDuration = '0 giờ 0 phút';

    // lặp tuần
    public $repeatDays = [];  // 0..6
    public $totalWeeks = 0;

    // ép duyệt khi trùng
    public bool $forceApprove = false;

    public function mount()
    {
        // Lưu labs dưới dạng MẢNG để Livewire không làm rơi collection
        $this->labs = Lab::select('code', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($lab) => [
                'code' => $lab->code,
                'name' => $lab->name,
            ])
            ->toArray();

        $this->users  = User::select('id', 'full_name', 'email')->orderBy('full_name')->get();
        $this->groups = Group::select('id', 'name')->orderBy('name')->get();

        // mặc định ngày + giờ
        $this->eventDate = now()->format('Y-m-d');
        $this->startTime = '08:00';
        $this->endTime   = '10:00';

        // mặc định phòng = phần tử đầu tiên (nếu có)
        if (!empty($this->labs) && empty($this->form['lab_code'])) {
            $this->form['lab_code'] = $this->labs[0]['code'] ?? '';
        }

        $this->calculateDuration();
        $this->composeDateTimes();
    }

    /* ===================== UPDATED ===================== */

    public function updatedEventDate()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    public function updatedStartTime()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    public function updatedEndTime()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    public function updatedFormRepeatUntil()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    public function updatedFormRepeatType()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    public function updatedRepeatDays()
    {
        $this->composeDateTimes();
        $this->calculateDuration();
    }

    /* ===================== TÍNH GIỜ ===================== */

    protected function composeDateTimes(): void
    {
        if ($this->eventDate && $this->startTime && $this->endTime) {
            $this->form['start'] = "{$this->eventDate} {$this->startTime}";
            $this->form['end']   = "{$this->eventDate} {$this->endTime}";
        }
    }

    protected function calculateDuration(): void
    {
        try {
            if (!$this->eventDate || !$this->startTime || !$this->endTime) {
                $this->totalDuration = '0 giờ 0 phút';
                $this->totalWeeks    = 0;
                return;
            }

            $start = Carbon::parse($this->eventDate.' '.$this->startTime);
            $end   = Carbon::parse($this->eventDate.' '.$this->endTime);

            if ($end->gt($start)) {
                $diffMin = $start->diffInMinutes($end);
                $hours   = intdiv($diffMin, 60);
                $minutes = $diffMin % 60;
                $this->totalDuration = "{$hours} giờ {$minutes} phút";

                if (($this->form['repeat_type'] ?? '') === 'weekly' && $this->form['repeat_until']) {
                    $repeatUntil = Carbon::parse($this->form['repeat_until'])->endOfDay();
                    $weeks = $start->diffInWeeks($repeatUntil) + 1;
                    $this->totalWeeks = max($weeks, 1);
                } else {
                    $this->totalWeeks = 0;
                }
            } else {
                $this->totalDuration = '0 giờ 0 phút';
                $this->totalWeeks    = 0;
            }
        } catch (\Throwable $e) {
            $this->totalDuration = '0 giờ 0 phút';
            $this->totalWeeks    = 0;
        }
    }

    /* ===================== VALIDATE ===================== */

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

            'form.repeat_type'  => 'nullable|in:daily,weekly,monthly',
            'form.repeat_until' => 'nullable|date|after_or_equal:form.start',

            'repeatDays'   => 'nullable|array',
            'repeatDays.*' => 'integer|between:0,6',

            'uploads'   => 'nullable|array|max:8',
            'uploads.*' => 'file|max:10240',
        ];
    }

    protected function messages()
    {
        return [
            // required chung
            'form.title.required'       => 'Trường này không được để trống.',
            'form.category.required'    => 'Trường này không được để trống.',
            'form.lab_code.required'    => 'Trường này không được để trống.',
            'form.user_id.required'     => 'Trường này không được để trống.',
            'form.start.required'       => 'Trường này không được để trống.',
            'form.end.required'         => 'Trường này không được để trống.',
            'form.status.required'      => 'Trường này không được để trống.',

            // thời gian
            'form.end.after'            => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'form.repeat_until.after_or_equal' => 'Ngày lặp đến phải sau hoặc bằng ngày bắt đầu.',

            // lặp tuần
            'repeatDays.array'          => 'Giá trị ngày lặp không hợp lệ.',
            'repeatDays.*.integer'      => 'Giá trị ngày lặp không hợp lệ.',

            // upload file
            'uploads.array'             => 'Danh sách tệp không hợp lệ.',
            'uploads.max'               => 'Bạn chỉ được chọn tối đa 8 tệp.',
            'uploads.*.file'            => 'Tệp tải lên không hợp lệ.',
            'uploads.*.max'             => 'Dung lượng mỗi tệp tối đa 10MB.',
        ];
    }

    /* ===================== FILE ===================== */

    public function removeUpload(int $index): void
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    /* ===================== CONFLICT ===================== */

    protected function hasConflict(string $labCode, Carbon $start, Carbon $end): bool
    {
        return LabEvent::where('status', 'approved')
            ->where('lab_code', $labCode)
            ->where(function ($q) use ($start, $end) {
                $q->where('start', '<', $end)
                    ->where('end',   '>', $start);
            })
            ->exists();
    }

    /* ===================== LẶP ===================== */

    protected function generateOccurrences(): array
    {
        $start    = Carbon::parse($this->form['start']);
        $end      = Carbon::parse($this->form['end']);
        $duration = $start->diffInMinutes($end);

        $repeatType  = $this->form['repeat_type'] ?? '';
        $repeatUntil = $this->form['repeat_until']
            ? Carbon::parse($this->form['repeat_until'])->endOfDay()
            : null;

        // không lặp
        if (!$repeatType || !$repeatUntil) {
            return [[
                'start' => $start->copy(),
                'end'   => $end->copy(),
            ]];
        }

        $occurrences = [];

        if ($repeatType === 'weekly' && !empty($this->repeatDays)) {
            // CAST repeatDays về int cho chắc (checkbox trả về string)
            $daysSelected = array_map('intval', $this->repeatDays);

            // lặp theo các thứ chọn
            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($repeatUntil)) {
                if (in_array($cursor->dayOfWeek, $daysSelected, true)) {
                    $occStart = $cursor->copy()->setTimeFromTimeString($start->format('H:i:s'));
                    $occEnd   = $occStart->copy()->addMinutes($duration);
                    $occurrences[] = ['start' => $occStart, 'end' => $occEnd];
                }
                $cursor->addDay();
            }
        } else {
            // daily/weekly/monthly cơ bản
            $curStart = $start->copy();
            while ($curStart->lte($repeatUntil)) {
                $occurrences[] = [
                    'start' => $curStart->copy(),
                    'end'   => $curStart->copy()->addMinutes($duration),
                ];

                if ($repeatType === 'daily') {
                    $curStart->addDay();
                } elseif ($repeatType === 'weekly') {
                    $curStart->addWeek();
                } elseif ($repeatType === 'monthly') {
                    $curStart->addMonthNoOverflow();
                } else {
                    break;
                }
            }
        }

        return $occurrences;
    }

    public function forceApprove()
    {
        $this->forceApprove = true;
        $this->createEvent();
    }

    /* ===================== CREATE ===================== */

    public function createEvent()
    {
        // Ghép lại start/end từ ngày + giờ đang chọn
        $this->composeDateTimes();

        // Validate input
        $this->validate();

        $start = Carbon::parse($this->form['start']);
        $end   = Carbon::parse($this->form['end']);

        // Bắt buộc cùng 1 ngày
        if ($start->toDateString() !== $end->toDateString()) {
            $this->dispatch('alert', type: 'error', message: 'Thời gian phải nằm trong cùng một ngày.');
            return;
        }

        // Sinh danh sách các mốc thời gian (kể cả quá khứ / tương lai)
        $occurrences = $this->generateOccurrences();

        if (empty($occurrences)) {
            $this->dispatch('alert', type: 'error', message: 'Không tạo được mốc thời gian nào, vui lòng kiểm tra lại ngày, giờ và lặp.');
            return;
        }

        // Nếu trạng thái là "Đã duyệt" và chưa bật ép duyệt thì check trùng lịch
        if ($this->form['status'] === 'approved' && !$this->forceApprove) {
            foreach ($occurrences as $occ) {
                if ($this->hasConflict($this->form['lab_code'], $occ['start'], $occ['end'])) {
                    // Gửi sự kiện mở modal xung đột + toast cảnh báo
                    $this->dispatch('open-conflict-modal');
                    $this->dispatch('alert', type: 'warning', message: 'Trùng khung giờ với lịch đã duyệt.');
                    return;
                }
            }
        }

        // Tạo sự kiện trong DB
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
                'color'          => null,
            ]);

            $createdEvents[] = $event;

            // Chỉ lưu file cho occurrence đầu tiên
            if ($index === 0 && !empty($this->uploads)) {
                foreach ($this->uploads as $file) {
                    if (!$file) {
                        continue;
                    }

                    $original = $file->getClientOriginalName();
                    $safeName = Str::random(6).'_'.time().'_'.preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $original);

                    $path = $file->storeAs('lab-events/'.$event->id, $safeName, 'public');

                    LabEventFile::create([
                        'lab_event_id' => $event->id,
                        'file_name'    => $original,
                        'file_path'    => $path,
                    ]);
                }
            }
        }

        // GIỮ LẠI PHÒNG LAB ĐANG CHỌN
        $currentLabCode = $this->form['lab_code'] ?? '';

        if (!$currentLabCode && !empty($this->labs)) {
            $currentLabCode = $this->labs[0]['code'] ?? '';
        }

        // Reset form sau khi tạo xong
        $this->form = [
            'title'         => '',
            'category'      => 'work',
            'lab_code'      => $currentLabCode, // giữ lab cũ
            'user_id'       => '',
            'group_id'      => '',
            'start'         => '',
            'end'           => '',
            'description'   => '',
            'status'        => 'approved',
            'repeat_type'   => '',
            'repeat_until'  => '',
        ];

        $this->eventDate      = now()->format('Y-m-d');
        $this->startTime      = '08:00';
        $this->endTime        = '10:00';
        $this->repeatDays     = [];
        $this->totalDuration  = '0 giờ 0 phút';
        $this->totalWeeks     = 0;
        $this->uploads        = [];
        $this->uploadIteration++;
        $this->forceApprove   = false;

        $count = count($createdEvents);
        $this->dispatch(
            'alert',
            type: 'success',
            message: "Đã tạo {$count} lịch thành công."
        );
    }

    public function render()
    {
        return view('livewire.lab-register')
            ->layout('components.layouts.admin-layout');
    }
}
