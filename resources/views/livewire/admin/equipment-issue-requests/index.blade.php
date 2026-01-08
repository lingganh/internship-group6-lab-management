<div>
    <div class="card">
        <div class="card-header py-3 d-flex justify-content-between">
            <div class="d-flex gap-2">
                <div>
                    <input wire:model.live="search" type="text" class="form-control"
                        placeholder="Tìm kiếm người báo hỏng">
                </div>
                <div>
                    <select wire:model.live="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="{{ \App\Models\EquipmentIssueRequest::STATUS_PENDING }}">Chờ xử lý</option>
                        <option value="{{ \App\Models\EquipmentIssueRequest::STATUS_IN_REVIEW }}">Đang xử lý</option>
                        <option value="{{ \App\Models\EquipmentIssueRequest::STATUS_COMPLETED }}">Đã hoàn thành</option>
                        <option value="{{ \App\Models\EquipmentIssueRequest::STATUS_CANCELLED }}">Đã hủy</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <button type="button" class="btn btn-light btn-icon px-2" @click="$wire.$refresh">
                        <i class="ph-arrows-clockwise px-1"></i><span>Tải lại</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive-md">
            <table class="table fs-table text-center">
                <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th class="text-start">NGƯỜI BÁO HỎNG</th>
                        <th>PHẢN HỒI</th>
                        <th>SỐ THIẾT BỊ</th>
                        <th>NGÀY TẠO</th>
                        <th>TRẠNG THÁI</th>
                        <th class="text-center">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $loop->index + 1 + $requests->perPage() * ($requests->currentPage() - 1) }}</td>

                            {{-- Người báo hỏng --}}
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        {{-- Avatar đơn giản (chữ cái đầu) – sau này có thể thay bằng Avatar component --}}
                                        <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <span class="fw-semibold">
                                                {{ \Illuminate\Support\Str::of($request->user->full_name ?? ($request->user->email ?? 'U'))->substr(0, 1)->upper() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $request->user->full_name ?? 'Không rõ' }}</div>
                                        <div class="text-muted fs-sm">{{ $request->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-start" style="max-width: 320px;">
                                @php $desc = (string) ($request->description ?? ''); @endphp

                                @if ($desc !== '')
                                    <span class="d-inline-block text-truncate" style="max-width: 320px;"
                                        title="{{ $desc }}">
                                        {{ \Illuminate\Support\Str::limit($desc, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $request->items_count }}</td>
                            <td>{{ $request->created_at ? $request->created_at->format('d-m-Y') : '' }}</td>

                            {{-- Trạng thái --}}
                            <td>
                                @php $status = $request->status; @endphp

                                @if ($status === \App\Models\EquipmentIssueRequest::STATUS_PENDING)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">Chờ xử lý</span>
                                @elseif($status === \App\Models\EquipmentIssueRequest::STATUS_IN_REVIEW)
                                    <span class="badge bg-info bg-opacity-10 text-info">Đang xử lý</span>
                                @elseif($status === \App\Models\EquipmentIssueRequest::STATUS_COMPLETED)
                                    <span class="badge bg-success bg-opacity-10 text-success">Đã hoàn thành</span>
                                @elseif($status === \App\Models\EquipmentIssueRequest::STATUS_CANCELLED)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Đã hủy</span>
                                @else
                                    <span class="badge bg-light text-muted">Không rõ</span>
                                @endif
                            </td>

                            {{-- Hành động --}}
                            <td class="text-center">
                                <div class="dropdown">
                                    <a href="#" class="text-body" data-bs-toggle="dropdown">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('admin.equipment-issue-requests.show', $request->id) }}"
                                            class="dropdown-item">
                                            <i class="ph-eye px-1"></i> Xem chi tiết
                                        </a>
                                        {{-- Sau này sẽ thêm Xóa / Chấp nhận ở đây --}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="7" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $requests->links('vendor.pagination.theme') }}
</div>
