<?php

namespace App\Livewire\Admin\Users;

use App\Common\Constants;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $search='';

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
}
