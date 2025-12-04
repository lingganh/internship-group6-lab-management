<?php

namespace App\Livewire\Admin\Groups;

use App\Models\Group;
use App\Models\Role as ModelsRole;
use App\Models\User;
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

    #[validate(as: 'Trưởng nhóm')]
    public int $leaderId=0;

    public $groupMembers=[];
    public $groupMemberId=0;

    public $quickViewUserId=null;

    public function render()
    {
        $users = User::where('sso_id' , '!=', null)
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
        ];
    }

    public function updatedleaderId(){
        if ($this->leaderId == 0) {
            $this->groupMembers = [];
            $this->groupMemberId = 0;
            return;
        }
        $this->groupMembers=[];
        $leader = User::find($this->leaderId);
        $this->groupMembers[] = $leader;
        $this->groupMemberId=0;
    }

    public function addUser($userId){
        if($userId == 0) return;
        $user = User::find($userId);
        if(!in_array($user, $this->groupMembers) && $user->id != $this->leaderId){
            $this->groupMembers[] = $user;
        }
        $this->groupMemberId=0;

    }

    public function removeUser($userId)
    {
        $user = User::find($userId);
        if(in_array($user, $this->groupMembers)){
            $this->groupMembers = array_filter($this->groupMembers, function($member) use ($userId) {
                return $member->id !== $userId;
            });
        }

    }

    public function save(){
        $this->validate();
        $this->dispatch('openModel', title:'Bạn có chắc chắn muốn tạo nhóm này không?', type:'warning', confirmEvent:'confirmCreateGroup');
    }

    public function confirmCreateGroup()
    {
        try {
            $group =  Group::create([
                'name' => $this->name,
                'description' => $this->description,
                'leader_id' => $this->leaderId,
            ]);
            if($group){
                $memberIds = array_map(function($member) {
                    return $member->id;
                }, $this->groupMembers);
                $group->users()->attach($memberIds);
            }

            session()->flash('success', 'Tạo nhóm người dùng thành công!');
            return redirect()->route('admin.groups.index', ['groupId' => $group->id]);

        }
        catch (\Exception $e) {
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', message: ' Tạo nhóm người dùng thất bại!', type: 'error');
        }
    }

    public function QuickView($userId)
    {
        $this->quickViewUserId = $userId;

    }

}
