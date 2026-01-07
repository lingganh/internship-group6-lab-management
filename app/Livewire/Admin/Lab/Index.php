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

        $this->dispatch(
            'openModel',
            type: 'warning',
            title: 'bạn có muốn xóa phòng lab nay không?',
            confirmEvent: 'confirmDeleteLab'
        );
    }


    public function confirmDeleteLab()
    {
        $lab = Lab::find($this->deleteId);

        if ($lab) {
            $lab->status = 'locked';
            $lab->save();

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'Đã chuyển phòng Lab sang trạng thái Tạm khóa!'
            );
        } else {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Phòng Lab không tồn tại!'
            );
        }

        $this->reset('deleteId');
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
