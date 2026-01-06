<?php

namespace App\Livewire\Admin\equipment;

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

    public function delete(int $id)
    {
        $eq = Equipment::find($id);

        if (!$eq) {
            return;
        }

        $eq->delete();

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Đã xóa thiết bị thành công.'
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

        $equipments = Equipment::query()
            ->with('lab')

            // SEARCH TỔNG
            ->when($search, function ($q) use ($search, $statusMap) {
                $q->where(function ($sub) use ($search, $statusMap) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhereHas('lab', function ($lab) use ($search) {
                            $lab->where('name', 'like', "%{$search}%");
                        });

                    foreach ($statusMap as $text => $value) {
                        if (str_contains($search, $text)) {
                            $sub->orWhere('status', $value);
                        }
                    }
                });
            })

            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->lab, fn($q) => $q->where('lab_id', $this->lab))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.equipment.index', [
            'labs' => Lab::orderBy('name')->get(),
            'equipments' => $equipments
        ])->layout('components.layouts.admin-layout');
    }
}
