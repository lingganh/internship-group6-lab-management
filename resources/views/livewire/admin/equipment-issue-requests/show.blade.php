<div>
    {{-- Thông tin phiếu tổng --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between">
            <div class="d-flex align-items-center gap-3">
                {{-- Avatar người gửi --}}
                <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;">
                    <span class="fw-semibold fs-4">
                        {{ \Illuminate\Support\Str::of($request->user->full_name ?? ($request->user->email ?? 'U'))->substr(0, 1)->upper() }}
                    </span>
                </div>
                <div>
                    <div class="fw-semibold">
                        {{ $request->user->full_name ?? 'Không rõ người báo hỏng' }}
                    </div>
                    <div class="text-muted">
                        {{ $request->user->email ?? '' }}
                    </div>
                    <div class="text-muted fs-sm">
                        Tạo lúc: {{ $request->created_at?->format('d-m-Y H:i') }}
                    </div>

                </div>
            </div>

            @php
                $total = $request->items->count();
                $pendingCnt = $request->items
                    ->where('status', \App\Models\EquipmentIssueRequestItem::STATUS_PENDING)
                    ->count();
                $approvedCnt = $request->items
                    ->where('status', \App\Models\EquipmentIssueRequestItem::STATUS_APPROVED)
                    ->count();
                $rejectedCnt = $request->items
                    ->where('status', \App\Models\EquipmentIssueRequestItem::STATUS_REJECTED)
                    ->count();
            @endphp
            <div class="text-end">
                <div class="mb-1">
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
                </div>
                <div class="text-muted fs-sm">
                    Tổng số thiết bị: <strong>{{ $total }}</strong>
                </div>
                <div class="fs-sm mt-1">
                    <span class="text-warning">Chờ: {{ $pendingCnt }}</span> ·
                    <span class="text-success">Đã chấp nhận: {{ $approvedCnt }}</span> ·
                    <span class="text-secondary">Đã từ chối: {{ $rejectedCnt }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Thông tin sử dụng phòng + mô tả chung --}}
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Thông tin sử dụng phòng</h6>

            @php $ev = $request->labEvent; @endphp
            @if ($ev)
                <span class="text-muted fs-sm">
                    {{ $ev->start?->format('d/m/Y H:i') }} → {{ $ev->end?->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>

        <div class="card-body">
            @php
                $ev = $request->labEvent;
                $lab = $ev?->lab;
            @endphp

            <div class="row g-3">
                {{-- Cột trái: thông tin event/lab --}}
                <div class="col-lg-6">
                    @if ($ev)
                        <div class="mb-2">
                            <div class="text-muted fs-sm">Sự kiện</div>
                            <div class="fw-semibold">{{ $ev->title }}</div>
                        </div>

                        <div class="mb-2">
                            <div class="text-muted fs-sm">Phòng/Lab</div>
                            <div class="fw-semibold">
                                {{ $lab?->name ?? $ev->lab_code }}
                                <span class="text-muted">({{ $ev->lab_code }})</span>
                            </div>

                            @if (!blank($lab?->location))
                                <div class="text-muted fs-sm">Vị trí: {{ $lab->location }}</div>
                            @endif
                        </div>

                        @if (!blank($ev->description))
                            <div class="mt-2">
                                <div class="text-muted fs-sm">Nội dung sử dụng</div>
                                <div class="border rounded p-2 bg-light" style="white-space: pre-wrap;">
                                    {{ $ev->description }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-muted">
                            Phiếu này do người dùng tạo ở trang chi tiết thiết bị.
                        </div>
                    @endif
                </div>

                {{-- Cột phải: mô tả chung (feedback) --}}
                <div class="col-lg-6">
                    <div class="text-muted fs-sm mb-1">Mô tả chung</div>

                    @if (!blank($request->description))
                        <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;">
                            {{ $request->description }}
                        </div>
                    @else
                        <div class="text-muted">—</div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- Danh sách thiết bị trong phiếu --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h6 class="mb-0">Danh sách thiết bị trong phiếu</h6>
        </div>

        <div class="table-responsive-md">
            <table class="table fs-table text-center">
                <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th class="text-start">THIẾT BỊ</th>
                        <th>MÔ TẢ</th>
                        <th>TRẠNG THÁI</th>
                        <th>TICKET</th>
                        <th class="text-center">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        @php
                            $images = is_array($item->images)
                                ? $item->images
                                : (json_decode($item->images ?? '[]', true) ?:
                                []);
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">
                                {{ $item->equipment->name ?? 'Thiết bị #' . $item->equipment_id }}
                            </td>
                            <td class="text-start" style="max-width: 260px;">
                                <span class="d-inline-block text-truncate" style="max-width: 260px;">
                                    {{ \Illuminate\Support\Str::limit((string) $item->description, 50) }}
                                </span>
                            </td>

                            <td>
                                @php $st = $item->status; @endphp
                                @if ($st === \App\Models\EquipmentIssueRequestItem::STATUS_PENDING)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">Chờ xử lý</span>
                                @elseif($st === \App\Models\EquipmentIssueRequestItem::STATUS_APPROVED)
                                    <span class="badge bg-success bg-opacity-10 text-success">Đã chấp nhận</span>
                                @elseif($st === \App\Models\EquipmentIssueRequestItem::STATUS_REJECTED)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Đã từ chối</span>
                                @else
                                    <span class="badge bg-light text-muted">Không rõ</span>
                                @endif
                            </td>

                            {{-- Ticket đã sinh ra --}}
                            <td>
                                @if ($item->equipment_issue_id)
                                    {{-- Tạm thời cho admin nhảy sang trang "Báo hỏng & lịch sử xử lý" của thiết bị --}}
                                    <a href="{{ route('client.equipment.issues.index', $item->equipment_id) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ph-ticket me-1"></i> Xem phiếu
                                    </a>
                                @else
                                    <span class="text-muted fs-sm">Chưa tạo</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    {{-- Xem chi tiết --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                        wire:click="showItem({{ $item->id }})" data-bs-toggle="modal"
                                        data-bs-target="#issueRequestItemModal">
                                        <i class="ph-eye me-1"></i>Xem
                                    </button>

                                    @php
                                        $isPending =
                                            $item->status === \App\Models\EquipmentIssueRequestItem::STATUS_PENDING;
                                    @endphp

                                    @if ($isPending)
                                        {{-- Chấp nhận --}}
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            wire:click="approveItem({{ $item->id }})"
                                            onclick="confirm('Chấp nhận tạo báo hỏng cho thiết bị này?') || event.stopImmediatePropagation()">
                                            <i class="ph-check me-1"></i>Chấp nhận
                                        </button>

                                        {{-- Từ chối --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="rejectItem({{ $item->id }})"
                                            onclick="confirm('Từ chối báo hỏng cho thiết bị này?') || event.stopImmediatePropagation()">
                                            <i class="ph-x me-1"></i>Từ chối
                                        </button>
                                    @else
                                        {{-- chỉ show nhãn --}}
                                        @if ($item->status === \App\Models\EquipmentIssueRequestItem::STATUS_APPROVED)
                                            <span class="badge bg-success bg-opacity-10 text-success align-self-center">
                                                Đã chấp nhận
                                            </span>
                                        @elseif($item->status === \App\Models\EquipmentIssueRequestItem::STATUS_REJECTED)
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary align-self-center">
                                                Đã từ chối
                                            </span>
                                        @endif
                                    @endif
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

    {{-- Modal xem chi tiết item --}}
    <div wire:ignore.self class="modal fade" id="issueRequestItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                @if ($selectedItem)
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Chi tiết báo hỏng –
                            {{ $selectedItem->equipment->name ?? 'Thiết bị #' . $selectedItem->equipment_id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- <p><strong>Tiêu đề:</strong> {{ $selectedItem->title }}</p> --}}
                        <p class="mb-2"><strong>Mô tả:</strong></p>
                        <div>
                            {{ $selectedItem->description }}
                        </div>
                        @php
                            $images = is_array($selectedItem->images)
                                ? $selectedItem->images
                                : (json_decode($selectedItem->images ?? '[]', true) ?:
                                []);
                        @endphp

                        <p class="mb-2 mt-2"><strong>Ảnh:</strong></p>
                        @if (count($images))
                            <div class="row">
                                @foreach ($images as $img)
                                    <div class="col-md-4 mb-3">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Ảnh báo hỏng"
                                            class="img-fluid rounded">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Không có ảnh đính kèm.</p>
                        @endif
                    </div>
                @else
                    <div class="modal-body">
                        <p class="text-muted mb-0">Không có dữ liệu.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
