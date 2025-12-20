<?php

namespace App\Livewire;

use Livewire\Component;

class LabDiary extends Component
{
    public function render()
    {
        return view('livewire.lab-diary')->layout('components.layouts.admin-layout');
    }
}
