<?php

namespace App\Livewire\Admin\EquipmentIssueRequests;

use App\Common\Constants;
use App\Models\EquipmentIssueRequest;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    /**
     * Reset về trang 1 khi filter thay đổi.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EquipmentIssueRequest::query()
            ->with(['user'])
            ->withCount('items')
            ->orderByDesc('created_at');

        // Filter theo trạng thái nếu có chọn
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        // Tìm kiếm theo tên người báo hỏng / email
        if ($this->search !== '') {
            $search = '%' . $this->search . '%';

            $query->whereHas('user', function ($q2) use ($search) {
                $q2->where('full_name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }


        $requests = $query->paginate(Constants::PER_PAGE_ADMIN);

        return view('livewire.admin.equipment-issue-requests.index', [
            'requests' => $requests,
        ]);
    }
}
