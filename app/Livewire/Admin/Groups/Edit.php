<?php

namespace App\Livewire\Admin\Groups;

use App\Enums\GroupStatus;
use App\Enums\UserStatus;
use App\Models\Group;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    protected $listeners = [
        'confirmEditGroup' => 'confirmEditGroup'
    ];
    public $groupId;

    #[validate(as: 'Tên nhóm')]
    public string $name='';

    #[validate(as: 'Mô tả')]
    public string $description='';

    #[validate(as: 'Giáo viên hướng dẫn')]
    public int $leaderId=0;

    public $statusGroup;
    public $created_at;

    public $groupMembers=[];
    public $groupMemberId=0;
    public $quickViewUserId=null;

    public $students = [];
    public function render()
    {
        $users = User::where('status' , '==', UserStatus::Approved->value)
            ->orWhere('email_verified_at', '!=', null)
            ->orderBy('full_name')->get();
        $status= GroupStatus::displayAll();
        return view('livewire.admin.groups.edit',
        [
            'users' => $users,
            'status'=>$status,
        ]);
    }

    public function mount()
    {
        $group = Group::find($this->groupId);
        if($group){
            $this->name = $group->name;
            $this->description = $group->description??'';
            $this->leaderId = $group->leader_id;
            $this->students = $group->students()->get()->toArray();
            $this->statusGroup = $group->status;
            $this->created_at = $group->created_at->format('d/m/Y H:i:s')??'';
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:groups,name,'. $this->groupId,
            'description' => 'nullable|string|max:1000',
            'leaderId' => 'required|exists:users,id',
            'students.*.code' => 'required|distinct:code',
            'students.*.full_name' => 'required',
            'students.*.class_name' => 'required',
        ];
    }

    protected $validationAttributes = [
        'students.*.full_name' => 'Họ và tên',
        'students.*.code' => 'Mã sinh viên',
        'students.*.class_name' => 'Lớp',
    ];

    public function addStudent()
    {
        $this->students[] = ['full_name' => '', 'code' => '', 'class_name' => ''];
    }

    public function removeStudent($index)
    {
        $this->resetErrorBag('students.'.$index.'.full_name');
        $this->resetErrorBag('students.'.$index.'.code');
        $this->resetErrorBag('students.'.$index.'.class_name');
        unset($this->students[$index]);
        $this->students = array_values($this->students);
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'students.')) {
            $this->validate([
                'students.*.code' => 'required|distinct:code',
                'students.*.full_name' => 'required',
                'students.*.class_name' => 'required',
            ]);
        } else {
            $this->validateOnly($propertyName);
        }
    }

//    public function addUser($userId){
//        if($userId == 0) return;
//        $user = User::find($userId);
//        if($user->id != $this->leaderId){
//            $this->groupMembers[] = $user;
//        }
//        $this->groupMemberId=0;
//    }
//
//    public function removeUser($userId)
//    {
//        $this->groupMembers = $this->groupMembers->reject(function ($member) use ($userId) {
//            return $member->id == $userId;
//        });
//
//    }

//    public function QuickView($userId)
//    {
//        $this->quickViewUserId = $userId;
//
//    }

    public function update(){
        $this->validate();
        $this->dispatch('openModel', title:'Bạn có chắc chắn muốn lưu chỉnh sửa nhóm này không?', type:'warning', confirmEvent:'confirmEditGroup');
    }

    public function confirmEditGroup(){
        DB::beginTransaction();

        try {
            $group = Group::find($this->groupId);
            if (!$group) {
                $this->dispatch('alert', type: 'error', message: 'Nhóm không tồn tại!');
                return null;
            }
            else{
                $group->name = $this->name;
                $group->description = $this->description;
                $group->leader_id = $this->leaderId;
                $group->status = $this->statusGroup ?? $group->status;
                $group->save();

                $studentIds = [];
                foreach ($this->students as $studentData) {
                    if (empty($studentData['code'])) {
                        continue;
                    }

                    // Tìm hoặc tạo mới student
                    $student = Student::firstOrCreate(
                        ['code'       => $studentData['code']],
                        [
                            'full_name'  => $studentData['full_name'],
                            'class_name' => $studentData['class_name'],
                            'code'       => $studentData['code']
                        ]
                    );

                    // Nếu student tồn tại rồi => update thông tin
                    $student->update([
                        'full_name'  => $studentData['full_name'],
                        'class_name' => $studentData['class_name']
                    ]);

                    $studentIds[] = $student->id;
                }
                $group->students()->sync($studentIds);
                DB::commit();

                Notification::create([
                    'user_id' => $this->leaderId, // RECEIVER: user_id của người nhận thông báo

                    // UI dùng để hiển thị: title + message (mô tả)
                    'title'   => 'Đã tạo  một nhóm Nghiên cứu khoa học mới!',
                    'message' => 'Bạn đã được thêm vào nhóm NCKH: ' . $this->name . ',Với vai trò là giảng viên hướng dẫn.',

                    // data: payload để UI biết ai gửi + click đi đâu + liên kết tới đối tượng nghiệp vụ
                    'data' => [
                        'request_id'  => Auth()->user()->id,
                        'sender_id'   => Auth()->user()->id,    // SENDER id
                        'sender_name' => Auth()->user()->full_name ?? $user->name ?? 'Người dùng', // SENDER display
                        'url'         => route('client.group-index'), // Click chuyển trang
                    ],
                ]);


                session()->flash('success','Chỉnh sửa nhóm thành công!');
                return redirect()->route('admin.groups.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', type:'error', message:'Đã xảy ra lỗi khi lưu chỉnh sửa nhóm: ');
        }
        return null;
    }
}
