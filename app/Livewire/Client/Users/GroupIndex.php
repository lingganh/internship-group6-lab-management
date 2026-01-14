<?php

namespace App\Livewire\Client\Users;

use App\Common\Constants;
use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;

class GroupIndex extends Component
{
use WithPagination;
    public $search = '';
    public $perPage = Constants::PER_PAGE_ADMIN;

    public function render()
    {

        $groups = Group::query()
            ->where('status', '!=', 'archived')
            ->where('leader_id', auth()->id()) // Chỉ lấy nhóm của mình
            ->when($this->search, function ($query) {
                // Chỉ cần tìm theo tên nhóm là đủ
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.client.users.group-index',[
            'groups' => $groups
        ]);
    }

    public function QuickView($id)
    {
        $this->dispatch('openModalQuickViewGroup', groupId: $id);
    }
}
