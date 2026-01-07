<div class="row">
    <div class="col-md-9 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-info"></i>
                Thông tin báo hỏng
            </div>
            <div class="card-body">
                {{-- thông báo flash --}}
                {{-- @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3 mx-3">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif --}}

                {{-- 1) Chọn thiết bị --}}
                <div class="mb-3">
                    <label class="col-form-label">
                        Thiết bị: <span class="required text-danger">*</span>
                    </label>
                    <select class="form-select @error('selectedEquipmentId') is-invalid @enderror"
                        wire:model.live="selectedEquipmentId">
                        <option value="">-- Chọn thiết bị --</option>
                        @foreach ($equipments as $equipment)
                            <option value="{{ $equipment->id }}">
                                {{ $equipment->name }}
                                @if ($equipment->code)
                                    ({{ $equipment->code }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('selectedEquipmentId')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2) Chỉ cho nhập thông tin khi đã chọn thiết bị --}}
                @if ($selectedEquipmentId)
                    {{-- Mô tả --}}
                    <div class="mb-3">
                        <label class="col-form-label">
                            Mô tả chi tiết: <span class="required text-danger">*</span>
                        </label>

                        <textarea class="form-control @error('description') is-invalid @enderror" rows="3" wire:model.live="description"></textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ảnh minh hoạ --}}
                    <div class="mb-3">
                        <label class="col-form-label">
                            Ảnh minh hoạ (tối đa 2 ảnh):
                        </label>
                        <input type="file"
                            class="form-control
                                @error('images') is-invalid @enderror
                                @error('images.*') is-invalid @enderror"
                            wire:model="images" multiple accept="image/*">

                        <div class="form-text">
                            Có thể chọn tối đa 2 ảnh cho mỗi thiết bị (jpg, jpeg, png, gif, webp – tối đa 2MB/ảnh).
                        </div>

                        @error('images')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                {{-- 3) Nút Thêm thiết bị --}}
                {{-- Ẩn nếu bị lỗi up ảnh --}}
                @php
                    $disableAdd =
                        !$selectedEquipmentId ||
                        !trim((string) $description) ||
                        $errors->has('images') ||
                        $errors->has('images.*');
                @endphp


                <button type="button" class="btn btn-success" wire:click="addItem" wire:loading.attr="disabled"
                    @disabled($disableAdd)>
                    Thêm thiết bị vào danh sách báo hỏng
                </button>

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
                                <th class="text-center" style="width: 60px;">STT</th>
                                <th style="width: 220px;">Thiết bị</th>
                                <th>Mô tả</th>
                                <th class="text-center" style="width: 160px;">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item['equipment_name'] }}</td>
                                    <td class="text-truncate" style="max-width: 260px;">
                                        {{ $item['description'] }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex justify-content-center gap-2">
                                            {{-- Xem chi tiết báo hỏng --}}
                                            <button type="button" class="btn btn-sm btn-warning"
                                                wire:click="showItem({{ $index }})" data-bs-toggle="modal"
                                                data-bs-target="#issuePreviewModal">
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
                    @if (!empty($viewingItem))
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Thiết bị:</div>
                            <div class="col-sm-8 fw-semibold">
                                {{ $viewingItem['equipment_name'] ?? '' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Mô tả chi tiết:</div>
                            <div class="col-sm-8">
                                {{ $viewingItem['description'] ?: '—' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Ảnh minh hoạ:</div>
                            <div class="col-sm-8">
                                @if (!empty($viewingItem['images']))
                                    <div class="row g-2">
                                        @foreach ($viewingItem['images'] as $imgPath)
                                            <div class="col-4">
                                                <img src="{{ asset('storage/' . $imgPath) }}"
                                                    class="img-fluid rounded border" alt="Ảnh báo hỏng">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">
                                        Không có ảnh minh hoạ.
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-muted">
                            Không có dữ liệu báo hỏng để hiển thị.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Panel danh sách item + nút Lưu --}}
    <div class="col-md-2 col-xl-1 col-12">
        <div class="card">
            <div class="card-header py-2">
                Hành động
            </div>
            <div class="card-body py-2">
                <button type="button" class="btn btn-sm btn-primary w-100" wire:click="saveRequest"
                    wire:loading.attr="disabled" @disabled(!count($items))>
                    Lưu
                </button>
            </div>
        </div>
    </div>

</div>
