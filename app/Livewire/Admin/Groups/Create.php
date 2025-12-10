<?php

namespace App\Livewire\Admin\Groups;

use App\Enums\UserStatus;
use App\Models\Group;
use App\Models\Role as ModelsRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;


class Create extends Component
{
    protected $listeners = [
        'confirmCreateGroup' => 'confirmCreateGroup'
    ];

    #[validate(as: 'Tên nhóm')]
    public string $name='';

    #[validate(as: 'Mô tả')]
    public string $description='';

    #[validate(as: 'Giảng viên hướng dẫn')]
    public int $leaderId=0;

    public $groupMembers=[];
    public $groupMemberId=0;

    public $quickViewUserId=null;

    public $students = [];

    public function render()
    {
        $users = User::where('status' , '==', UserStatus::Approved->value)
            ->orWhere('email_verified_at', '!=', null)
            ->orderBy('full_name')->get();
        return view('livewire.admin.groups.create',
        [
            'users' => $users,
        ]);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:groups,name',
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

    public function mount(){
        $this->students[] = ['full_name' => '', 'code' => '', 'class_name' => ''];
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

//    public function updatedleaderId(){
//        if ($this->leaderId == 0) {
//            $this->groupMembers = [];
//            $this->groupMemberId = 0;
//            return;
//        }
//        $this->groupMembers=[];
//        $leader = User::find($this->leaderId);
//        $this->groupMembers[] = $leader;
//        $this->groupMemberId=0;
//    }

//    public function addUser($userId){
//        if($userId == 0) return;
//        $user = User::find($userId);
//        if(!in_array($user, $this->groupMembers) && $user->id != $this->leaderId){
//            $this->groupMembers[] = $user;
//        }
//        $this->groupMemberId=0;
//
//    }

//    public function removeUser($userId)
//    {
//        $user = User::find($userId);
//        if(in_array($user, $this->groupMembers)){
//            $this->groupMembers = array_filter($this->groupMembers, function($member) use ($userId) {
//                return $member->id !== $userId;
//            });
//        }
//    }

    public function save(){
        $this->validate();
        $this->dispatch('openModel', title:'Bạn có chắc chắn muốn tạo nhóm này không?', type:'warning', confirmEvent:'confirmCreateGroup');
    }

    public function confirmCreateGroup()
    {
        DB::beginTransaction();
        try {
            $group =  Group::create([
                'name' => $this->name,
                'description' => $this->description,
                'leader_id' => $this->leaderId,
            ]);
            if($group && !empty($this->students)){
                foreach ($this->students as $studentData){
                    if(empty($studentData['code'])) continue;
                    $student = Student::firstOrCreate(
                        [   'code'       => $studentData['code'],
                            'full_name'  => $studentData['full_name'],
                            'class_name' => $studentData['class_name']
                        ],
                        [
                            'full_name' => $studentData['full_name'],
                            'class_name' => $studentData['class_name'],
                            'code' => $studentData['code']
                        ]
                    );
                    $group->students()->attach($student->id);
                }
            }

            DB::commit();

            session()->flash('success', 'Tạo nhóm NCKH mới thành công!');
            return redirect()->route('admin.groups.index', ['groupId' => $group->id]);

        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', message: ' Tạo nhóm người dùng thất bại!', type: 'error');
        }
        return null;
    }

//    public function QuickView($userId)
//    {
//        $this->quickViewUserId = $userId;
//
//    }

}
