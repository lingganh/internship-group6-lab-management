<?php

namespace App\Livewire\Admin\Groups;

use App\Enums\GroupStatus;
use App\Models\Group;
use App\Models\User;
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

    #[validate(as: 'Trưởng nhóm')]
    public int $leaderId=0;

    public $statusGroup;
    public $created_at;

    public $groupMembers=[];
    public $groupMemberId=0;
    public $quickViewUserId=null;

    public function render()
    {
        $users = User::all();
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
            $this->groupMembers = $group->users()->get();
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
        ];
    }

    public function addUser($userId){
        if($userId == 0) return;
        $user = User::find($userId);
        if($user->id != $this->leaderId){
            $this->groupMembers[] = $user;
        }
        $this->groupMemberId=0;
    }

    public function removeUser($userId)
    {
        $this->groupMembers = $this->groupMembers->reject(function ($member) use ($userId) {
            return $member->id == $userId;
        });

    }

    public function QuickView($userId)
    {
        $this->quickViewUserId = $userId;

    }

    public function update(){
        $this->validate();
        $this->dispatch('openModel', title:'Bạn có chắc chắn muốn lưu chỉnh sửa nhóm này không?', type:'warning', confirmEvent:'confirmEditGroup');
    }

    public function confirmEditGroup(){
        try {
            $group = Group::find($this->groupId);
            if($group){
                $group->name = $this->name;
                $group->description = $this->description;
                $group->leader_id = $this->leaderId;
                $group->status = $this->statusGroup;
                $group->save();
                $group->users()->sync(collect($this->groupMembers)->pluck('id')->toArray());
                session()->flash('success','Chỉnh sửa nhóm thành công!');
                return redirect()->route('admin.groups.index');
            } else {
                $this->dispatch('alert', type:'error', message:'Nhóm không tồn tại!');
            }
        } catch (\Exception $e) {
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', type:'error', message:'Đã xảy ra lỗi khi lưu chỉnh sửa nhóm: ');
        }
    }
}
