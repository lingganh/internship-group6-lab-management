<?php

namespace App\Livewire\Admin\Lab;

use Livewire\Component;
use App\Models\Lab;
use Livewire\WithPagination;
class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $status='';
    public $deleteId = null;
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function openDeleteModal($id)
    {
        $this->deleteId = $id;

        $this->dispatch('openModel',
            type: 'warning',
            title: 'Xóa phòng Lab này?',
            confirmEvent: 'confirmDeleteLab'
        );
    }

    public function confirmDeleteLab()
    {
        Lab::findOrFail($this->deleteId)->delete();
        $this->reset('deleteId');
        session()->flash('success', 'Xóa phòng Lab thành công');

    }

    public function render()
    {

        $statusMap = [
            'hoạt động' => 'active',
            'active' => 'active',

            'bảo trì' => 'maintenance',
            'maintenance' => 'maintenance',

            'khóa' => 'locked',
            'tạm khóa' => 'locked',
            'locked' => 'locked',
        ];

        $statusSearch = null;

        $key = strtolower(trim($this->search));

        if (array_key_exists($key, $statusMap)) {
            $statusSearch = $statusMap[$key];
        }

        $labs = Lab::when($this->search, function ($query) use ($statusSearch) {
            $query->where(function ($q) use ($statusSearch) {
                $q->where('name','like','%'.$this->search.'%')
                    ->orWhere('code','like','%'.$this->search.'%');

                if ($statusSearch) {
                    $q->orWhere('status', $statusSearch);
                }
            });
        })
            ->orderBy('name','asc')
            ->paginate($this->perPage);

        return view('livewire.admin.lab.index', [
            'labs' => $labs
        ])->layout('components.layouts.admin-layout');
    }
}
