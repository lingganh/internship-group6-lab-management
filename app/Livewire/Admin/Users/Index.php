<?php

namespace App\Livewire\Admin\Users;

use App\Common\Constants;
use App\Enums\UserStatus;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    protected $listeners = [
        'confirmDeleteUser' => 'deleteUser',
        'confirmApproveUser' => 'approveUser',
        'confirmArchiveUsers' => 'archiveUsers',
        'confirmRearchiveUsers' => 'rearchiveUsers',
    ];

    use WithPagination;
    public $search='';
    public $deleteId=null;
    public $approveId=null;
    public $archiveId=null;

    public $perPage = Constants::PER_PAGE_ADMIN;

    public $status = [];

    public function render()
    {
        $users = User::query()
            ->when(!empty($this->status), function ($query) {
                $query->whereIn('status', $this->status);
            }, function ($q) {
                $q->where('status', '!=', UserStatus::Archived->value);
            })
            ->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('role_id', 'asc')
            ->paginate($this->perPage);

        $userStatus = UserStatus::displayAll();

        return view('livewire.admin.users.index',[
            'users' => $users,
            'userStatus' => $userStatus,
        ]);
    }

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn xóa người dùng này không?',confirmEvent:'confirmDeleteUser' );

    }

    public function deleteUser(){
        User::find($this->deleteId)->delete();
        $this->dispatch('alert', type:'success', message:'Xóa người dùng thành công!');
    }

    public function openApproveModal($id)
    {
        $this->approveId = $id;
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn duyệt tài khoản này không',confirmEvent:'confirmApproveUser' );
    }

    public function approveUser(){
        $user = User::find($this->approveId);
        $user->status = UserStatus::Approved->value;
        $user->save();
        $this->dispatch('alert', type:'success', message:'Duyệt tài khoản thành công!');
    }

    public function openArchiveModal($id){
        $this->archiveId = $id;
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn lưu trữ người dùng này không?',confirmEvent:'confirmArchiveUsers' );
    }

    public function archiveUsers()
    {
        $user = User::find($this->archiveId);
        $user->status = UserStatus::Archived->value;
        $user->save();
        $this->dispatch('alert', type:'success', message:'Lưu trữ tài khoản thành công!');
    }

    public function openRearchiveModal($id){
        $this->archiveId = $id;
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn khôi phục người dùng này không?',confirmEvent:'confirmRearchiveUsers' );
    }

    public function rearchiveUsers()
    {
        $user = User::find($this->archiveId);
        $user->status = UserStatus::Approved->value;
        $user->save();
        $this->dispatch('alert', type:'success', message:'Khôi phục tài khoản thành công!');
    }
}
