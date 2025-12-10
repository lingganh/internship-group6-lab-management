<?php

namespace App\Livewire\Admin\Groups;

use App\Common\Constants;
use App\Enums\GroupStatus;
use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    protected $listeners = [
        'confirmDeleteGroup' => 'DeleteGroup'
    ];
    use WithPagination;
    public $search = '';
    public $deleteGroupId = null;

    public $perPage = Constants::PER_PAGE_ADMIN;


    public function render()
    {
        $groups = Group::query()
            ->where('status', '!=', 'archived')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('leader', function ($q2) {
                            $q2->where('full_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->paginate($this->perPage);
        return view('livewire.admin.groups.index',
            ['groups' => $groups]
        );
    }

    public function openDeleteModal($id){
        $this->deleteGroupId = $id;
        $this->dispatch('openModel', type:'warning', title:'Bạn có chắc chắn muốn xóa nhóm này không?', confirmEvent:'confirmDeleteGroup');
    }

    public function deleteGroup(){
        $group = Group::find($this->deleteGroupId);
        if($group){
            $group->status = GroupStatus::Archived->value;
            $group->save();
            $this->dispatch('alert', type:'success', message:'Xóa nhóm thành công!');
        } else {
            $this->dispatch('alert', type:'error', message:'Nhóm không tồn tại hoặc đã bị xóa!');
        }
    }
}
