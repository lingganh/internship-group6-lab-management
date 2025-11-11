<?php

namespace App\Livewire\Admin\Users;

use App\Common\Constants;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    protected $listeners = [
        'confirmDeleteUser' => 'deleteUser',
    ];

    use WithPagination;
    public $search='';
    public $deleteId=null;

    public function render()
    {
        $users = User::where('full_name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orderBy('role_id', 'asc')
            ->paginate(Constants::PER_PAGE_ADMIN);

        return view('livewire.admin.users.index',[
            'users' => $users,
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
}
