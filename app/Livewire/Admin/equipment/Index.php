<?php

namespace App\Livewire\Admin\equipment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lab;
use App\Models\LabEquipmentItem;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['confirmDeleteEquipment'];

    public $search = '';
    public $lab = '';
    public $perPage = 10;
    public $deleteId;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLab() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;

        $this->dispatch(
            'openModel',
            type: 'warning',
            title: 'Bạn có muốn xóa thiết bị khỏi phòng lab này không?',
            confirmEvent: 'confirmDeleteEquipment'
        );
    }

    public function confirmDeleteEquipment()
    {
        $item = LabEquipmentItem::with(['equipment', 'lab'])->find($this->deleteId);

        if (!$item) {
            $this->dispatch('alert', type: 'error', message: 'Bản ghi không tồn tại!');
            return;
        }

        $item->delete();

        $this->dispatch('alert', type: 'success', message: 'Xóa thiết bị khỏi phòng lab thành công!');
        $this->reset('deleteId');
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        $items = LabEquipmentItem::query()
            ->with(['lab', 'equipment'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('equipment', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%")
                           ->orWhere('code', 'like', "%{$search}%")
                           ->orWhere('type', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lab', function ($lab) use ($search) {
                        $lab->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                });
            })
            ->when($this->lab, fn ($q) => $q->where('lab_id', $this->lab))
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.admin.equipment.index', [
            'labs' => Lab::orderBy('name')->get(),
            'items' => $items,
        ])->layout('components.layouts.admin-layout');
    }
}
