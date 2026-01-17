<?php

namespace App\Livewire\Client\Users;

use App\Models\Group;
use Livewire\Component;

class QuickviewGroup extends Component
{
    protected $listeners = [
        'openModalQuickViewGroup' => 'openModalQuickViewGroup'
    ];

    public $id;

    public function render()
    {
        $group = Group::find($this->id);

        return view('livewire.client.users.quickview-group',[
            'group' => $group
        ]);
    }

    public function openModalQuickViewGroup($groupId)
    {
        $this->reset('id');
        $this->id = $groupId;
    }
}
