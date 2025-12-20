<?php

namespace App\Livewire\Admin\Equipment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipment;
use App\Models\Lab;


class Index extends Component
{
    use  WithPagination;

    protected $paginationTheme = 'bootstrap';


    public $search = '';
    public $lab = '';
    public $status = '';
    public $perPage =10;

    public function updated($field)
    {
        if(in_array($field, ["status", "lab", "search"])){
            $this->resetPage();
        }
    }

    public function delete(int $id)
    {
        $eq = Equipment::find($id);

        if($eq) return;

        $eq->delete();

        $this->dispatch(
            'notify',
            type:'success',
            message: 'Đã xóa thiết bị thàng công.'
        );
    }



    public function render()
    {
        $equipments = Equipment::with('lab')
            ->when($this->search, function ($q)
            {
                $q->where(fn($sub) =>
                $sub->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('type', 'like', '%'.$this->search.'%')
                );
            })
            ->when($this->lab, fn($q) => $q->where('lab_id', $this->lab))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.equipment.index',[
            'labs' => Lab::orderBy('name')->get(),
            'equipments' => $equipments
        ])->layout('components.layouts.admin-layout');
    }
}
