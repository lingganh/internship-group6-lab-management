<?php

namespace App\Livewire\Admin\equipment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipment;
use App\Models\Lab;
use App\Models\LabEquipmentItem;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use  WithPagination;

    protected $paginationTheme = 'bootstrap';


    public $search = '';
    public $lab = '';
    public $status = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLab()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public $deleteId;

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;

        $this->dispatch(
            'openModel',
            type: 'warning',
            title: 'Bạn có muốn xóa thiết bị này không?',
            confirmEvent: 'confirmDeleteEquipment'
        );
    }

    public function delete()
    {
        $eq = Equipment::with('labItems')->find($this->deleteId);

        if (! $eq) return;

        DB::transaction(function () use ($eq) {
            $eq->labItems()->delete();
            $eq->delete();
        });

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Xoá thiết bị thành công!'
        );
    }




    public function render()
    {
        $search = trim(mb_strtolower($this->search));

        $statusMap =
            [
            'sẵn sàng sử dụng' => 'available',
            'đang sử dụng' => 'in_use',
            'bảo trì' => 'maintenance',
            'hỏng' => 'broken',
        ];

        $items = LabEquipmentItem::query()
            ->with(['lab', 'equipment'])

            ->when($search, function($q) use ($search, $statusMap) {
                $q->where(function($sub) use ($search, $statusMap) {
                    $sub->whereHas('equipment', fn($eq) => $eq
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                    )
                        ->orWhereHas('lab', fn($lab) => $lab->where('name', 'like', "%{$search}%"));

                    foreach ($statusMap as $text => $value) {
                        if (str_contains($search, $text)) {
                            $sub->orWhereHas('equipment', fn($eq) => $eq->where('status', $value));
                        }
                    }
                });
            })


            ->when($this->status, fn($q) => $q->whereHas('equipment', fn($eq) => $eq->where('status', $this->status)))


            ->when($this->lab, fn($q) => $q->where('lab_id', $this->lab))

            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.equipment.index', [
            'labs' => Lab::orderBy('name')->get(),
            'items' => $items,
        ])->layout('components.layouts.admin-layout');
    }
}
