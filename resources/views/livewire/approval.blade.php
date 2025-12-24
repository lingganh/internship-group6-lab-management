<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">Phê Duyệt Lịch Đăng Ký</h5>
                    <span class="badge bg-warning text-dark px-3 py-2">
                        Đang chờ: {{ $pendingCount }} lịch
                    </span>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4 p-3 bg-light rounded shadow-sm">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Trạng thái</label>
                            <select wire:model.live="filterStatus" class="form-select border-0 shadow-sm">
                                <option value="pending">Chờ phê duyệt</option>
                                <option value="approved">Đã phê duyệt</option>
                                <option value="rejected">Đã từ chối</option>
                                <option value="">Tất cả</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Người đăng ký</label>
                            <select wire:model.live="filterUserId" class="form-select border-0 shadow-sm">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ngày đăng ký</label>
                            <input type="date" wire:model.live="filterDate" class="form-control border-0 shadow-sm">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sự kiện</th>
                                    <th>Người đăng ký</th>
                                    <th>Thời gian</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->title }}</div>
                                            <small class="text-muted">{{ $item->category }}</small>
                                        </td>
                                        <td>{{ $item->user?->full_name ?? 'N/A' }}</td> <td>
                                            <div class="small fw-bold">{{ $item->start->format('d/m/Y') }}</div>
                                            <div class="small text-muted">{{ $item->start->format('H:i') }} - {{ $item->end->format('H:i') }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($item->status === 'pending')
                                                <span class="badge rounded-pill bg-warning text-dark px-3">Chờ duyệt</span>
                                            @elseif($item->status === 'approved')
                                                <span class="badge rounded-pill bg-success px-3">Đã duyệt</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger px-3">Từ chối</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="viewSchedule({{ $item->id }})" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetails">
                                                Chi tiết
                                            </button>
                                            @if($item->status === 'pending')
                                                <button wire:click="rejectSchedule({{ $item->id }})" wire:confirm="Xác nhận từ chối lịch này?" class="btn btn-outline-danger btn-sm ms-1">
                                                    Từ chối
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Không có dữ liệu phù hợp.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $schedules->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Chi Tiết Đăng Ký</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($selectedSchedule)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Tiêu đề / Phân loại:</label>
                                <p class="fw-bold mb-0">{{ $selectedSchedule->title }}</p>
                                <small class="text-info">{{ $selectedSchedule->category }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Người đăng ký:</label>
                                <p class="fw-bold mb-0">{{ $selectedSchedule->user?->full_name ?? 'N/A' }}</p>
                                <small class="text-muted">{{ $selectedSchedule->user?->email }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Thời gian:</label>
                            <p class="fw-bold text-primary">{{ $selectedSchedule->start->format('H:i d/m/Y') }} — {{ $selectedSchedule->end->format('H:i d/m/Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Mô tả:</label>
                            <div class="p-2 bg-light border rounded small">{{ $selectedSchedule->description ?? 'Không có mô tả.' }}</div>
                        </div>
                        
                        <div>
                            <label class="text-muted small fw-bold">Tệp đính kèm ({{ $selectedSchedule->files->count() }}):</label>
                            <div class="list-group mt-1">
                                @forelse($selectedSchedule->files as $file)
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="list-group-item list-group-item-action small py-2">
                                        <i class="far fa-file-alt text-primary me-2"></i> {{ $file->file_name }}
                                    </a>
                                @empty
                                    <p class="text-muted small italic p-2 border rounded bg-light">Không có tệp kèm theo.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($selectedSchedule && $selectedSchedule->status === 'pending')
                        <button wire:click="approveSchedule" class="btn btn-success px-4" data-bs-dismiss="modal">Phê Duyệt</button>
                        <button wire:click="rejectSchedule" wire:confirm="Xác nhận từ chối lịch này?" class="btn btn-danger px-4" data-bs-dismiss="modal">Từ Chối</button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('close-modal', () => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalDetails'));
        if (modal) modal.hide();
    });
</script>