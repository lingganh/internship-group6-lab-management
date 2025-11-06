<?php

namespace App\Livewire\Admin\Groups;

use App\Models\User;
use Livewire\Component;

class QuickView extends Component
{
    public $userId=null;
    public function render()
    {
        if($this->userId){
            $user = User::find($this->userId);
        }
        return view('livewire.admin.groups.quick-view',[
            'user' => $user ?? null,
        ]);
    }
}
