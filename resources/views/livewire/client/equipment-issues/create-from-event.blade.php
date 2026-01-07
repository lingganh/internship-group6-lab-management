<div class="row">
    <div class="col-md-12 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-info"></i>
                Thông tin báo hỏng
            </div>
            <div class="card-body">
                {{-- Mô tả chung (bắt buộc) --}}
                <div class="mb-3">
                    <label class="form-label">Mô tả chung <span class="text-danger">*</span></label>
                    <textarea class="form-control" rows="3" wire:model.live="commonDescription" placeholder="Nhập mô tả chung..."></textarea>
                    @error('commonDescription')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Chọn thiết bị --}}
                <div class="mb-3">
                    <label class="form-label">Thiết bị <span class="text-danger">*</span></label>
                    <select class="form-select" wire:model.live="selectedEquipmentId">
                        <option value="">-- Chọn thiết bị --</option>
                        @foreach ($this->selectableEquipmentOptions as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                    @error('selectedEquipmentId')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Chỉ hiện khi đã chọn thiết bị --}}
                @if ($selectedEquipmentId)
                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="3" wire:model.live="description" placeholder="Mô tả lỗi của thiết bị..."></textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh minh hoạ (tối đa 2 ảnh)</label>
                        <input type="file" class="form-control" wire:model="images" multiple accept="image/*">
                        @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-success" wire:click="addItem">
                        Thêm thiết bị vào danh sách báo hỏng
                    </button>
                @endif

            </div>

        </div>

        <div class="card mt-3">
            <div class="card-header">
                Danh sách báo hỏng đã thêm ({{ count($items) }})
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 30px;">STT</th>
                                <th style="width: 120px;">Thiết bị</th>
                                <th style="width: 220px;">Mô tả</th>
                                <th class="text-center" style="width: 160px;">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item['equipment_label'] ?? '#' . ($item['equipment_id'] ?? '') }}</td>
                                    <td class="text-muted text-break" title="{{ $item['description'] ?? '' }}">
                                        {{ \Illuminate\Support\Str::limit($item['description'] ?? '', 50, '…') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex justify-content-center gap-2">
                                            {{-- Xem chi tiết báo hỏng --}}
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                wire:click="showItem({{ $index }})">
                                                <i class="ph-eye me-1"></i> Xem
                                            </button>

                                            {{-- Xóa hàng --}}
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="removeItem({{ $index }})">
                                                <i class="ph-trash me-1"></i> Xóa
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        {{-- hiển thị không có DL --}}
                                        <img src="{{ asset('assets/images/illustrations/empty-state.svg') }}"
                                            alt="" class="h-120px mb-2">
                                        <div class="text-muted">Chưa có thiết bị nào được thêm.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>

    @teleport('body')
        {{-- Modal xem chi tiết báo hỏng --}}
        <div wire:ignore.self class="modal fade" id="issuePreviewModal" tabindex="-1"
            aria-labelledby="issuePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="issuePreviewModalLabel">
                            Thông tin báo hỏng thiết bị
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if (!empty($previewItem))
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Thiết bị:</div>
                                <div class="col-sm-8 fw-semibold">
                                    {{ $previewItem['equipment_label'] ?? '' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Mô tả chi tiết:</div>
                                <div class="col-sm-8 text-break">
                                    {{ $previewItem['description'] ?: '—' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Ảnh minh hoạ:</div>
                                <div class="col-sm-8">
                                    @if (!empty($previewItem['images']))
                                        <div class="row g-2">
                                            @foreach ($previewItem['images'] as $imgPath)
                                                <div class="col-4">
                                                    <img src="{{ asset('storage/' . $imgPath) }}"
                                                        class="img-fluid rounded border" alt="Ảnh báo hỏng">
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Không có ảnh minh hoạ.</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-muted">Không có dữ liệu báo hỏng để hiển thị.</div>
                        @endif
                    </div>


                </div>
            </div>
        </div>
    @endteleport
</div>
