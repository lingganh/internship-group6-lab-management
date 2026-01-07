<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Báo hỏng & lịch sử xử lý
            <small class="text-muted">Thiết bị #{{ $equipmentId }}</small>
        </h5>

        {{-- Nút mở modal báo hỏng --}}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIssueModal">
            Báo hỏng
        </button>
    </div>

    <div class="card-body">

        {{-- Flash message thành công --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Danh sách báo hỏng --}}
        @if ($issues->count())
            <ul class="list-group list-group-flush">
                @foreach ($issues as $issue)
                    @php
                        $reporter = $issue->reporter;
                    @endphp

                    <li class="list-group-item">
                        <div class="d-flex">
                            {{-- Avatar  --}}
                            <div class="me-3">
                                @if ($reporter)
                                    <img src="{{ Avatar::create($reporter->full_name ?? ($reporter->email ?? 'User'))->toBase64() }}"
                                        class="rounded-circle" width="40" height="40"
                                        alt="{{ $reporter->full_name ?? 'Người dùng' }}">
                                @else
                                    <img src="{{ asset('assets/images/default-user-image.png') }}"
                                        class="rounded-circle" width="40" height="40" alt="Người dùng">
                                @endif
                            </div>

                            <div class="flex-fill">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        {{-- Tên user --}}
                                        <div class="fw-semibold">
                                            {{ $reporter->full_name ?? 'Người dùng' }}
                                        </div>

                                        <div class="small text-muted">
                                            {{-- Chỉ hiện mã phiếu với admin --}}
                                            @if (auth()->user()?->hasRole('admin'))
                                                Mã Phiếu #{{ $issue->id }}
                                            @endif
                                            {{-- thời gian --}}
                                            @if ($issue->created_at)
                                                Tạo {{ $issue->created_at->diffForHumans() }}
                                            @endif
                                        </div>


                                    </div>

                                    <div class="text-end">


                                        {{-- Status --}}
                                        @php
                                            $st = (string) $issue->status;

                                            $stLabel = match ($st) {
                                                'pending' => 'Chờ xử lý',
                                                'in_progress' => 'Đang xử lý',
                                                'resolved' => 'Đã xử lý',
                                                'closed' => 'Đã đóng',
                                                'rejected' => 'Từ chối',
                                                'cancelled' => 'Đã hủy',
                                                default => $st,
                                            };

                                            $stClass = match ($st) {
                                                'pending' => 'bg-warning',
                                                'in_progress' => 'bg-primary',
                                                'resolved' => 'bg-success',
                                                'closed' => 'bg-dark',
                                                'rejected' => 'bg-danger',
                                                'cancelled' => 'bg-secondary',
                                                default => 'bg-secondary',
                                            };
                                        @endphp

                                        <span class="badge {{ $stClass }}">{{ $stLabel }}</span>
                                    </div>
                                </div>

                                {{-- Nội dung mô tả --}}
                                <p class="mt-2 mb-1">
                                    {{ $issue->description }}
                                </p>

                                {{-- Ảnh đính kèm --}}
                                @if (is_array($issue->images) && count($issue->images))
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @foreach ($issue->images as $path)
                                            <img src="{{ asset($path) }}" alt="Ảnh báo hỏng"
                                                style="width: 120px; height: 80px; object-fit: cover; cursor: zoom-in;"
                                                class="img-thumbnail" data-bs-toggle="modal"
                                                data-bs-target="#imagePreviewModal" data-full="{{ asset($path) }}">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Lịch sử cập nhật: chỉ hiển thị nếu có log --}}
                                @if ($issue->logs->count())
                                    <div class="mt-2">
                                        <a class="small text-decoration-none" data-bs-toggle="collapse"
                                            href="#issue-history-{{ $issue->id }}" role="button"
                                            aria-expanded="false" aria-controls="issue-history-{{ $issue->id }}">
                                            <i class="ph-clock-counter-clockwise me-1"></i>
                                            Lịch sử cập nhật ({{ $issue->logs->count() }})
                                        </a>

                                        <div class="collapse mt-2" id="issue-history-{{ $issue->id }}">
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($issue->logs as $log)
                                                    <li class="mb-2">
                                                        <div class="d-flex">
                                                            {{-- Avatar người cập nhật --}}
                                                            <div class="me-2">
                                                                @if ($log->changer)
                                                                    <img src="{{ Avatar::create($log->changer->full_name ?? ($log->changer->email ?? 'Admin'))->toBase64() }}"
                                                                        class="rounded-circle" width="28"
                                                                        height="28"
                                                                        alt="{{ $log->changer->full_name ?? 'Admin' }}">
                                                                @else
                                                                    <img src="{{ asset('assets/images/default-user-image.png') }}"
                                                                        class="rounded-circle" width="28"
                                                                        height="28" alt="Admin">
                                                                @endif
                                                            </div>

                                                            <div class="flex-fill small border-start ps-2">
                                                                {{-- Dòng trên: thời gian + tên người cập nhật --}}
                                                                <div class="text-muted">
                                                                    {{ $log->created_at?->format('d/m/Y H:i') }}
                                                                    @if ($log->changer)
                                                                        ·
                                                                        {{ $log->changer->full_name ?? 'Admin' }}
                                                                    @endif
                                                                </div>

                                                                @php
                                                                    $statusChanged =
                                                                        $log->from_status !== $log->to_status;
                                                                @endphp

                                                                @php
                                                                    $mapStatus = function ($st) {
                                                                        $st = (string) $st;

                                                                        return match ($st) {
                                                                            'pending' => 'Chờ xử lý',
                                                                            'in_progress' => 'Đang xử lý',
                                                                            'resolved' => 'Đã xử lý',
                                                                            'closed' => 'Đã đóng',
                                                                            'rejected' => 'Từ chối',
                                                                            'cancelled' => 'Đã hủy',
                                                                            'approved' => 'Đã chấp nhận',
                                                                            default => $st !== '' ? $st : '—',
                                                                        };
                                                                    };
                                                                @endphp

                                                                @if ($statusChanged)
                                                                    <div>
                                                                        Trạng thái:
                                                                        <span
                                                                            class="fw-semibold">{{ $mapStatus($log->from_status) }}</span>
                                                                        →
                                                                        <span
                                                                            class="fw-semibold">{{ $mapStatus($log->to_status) }}</span>
                                                                    </div>
                                                                @endif




                                                                @if ($log->notes)
                                                                    <div class="mt-1">
                                                                        Ghi chú xử lý:
                                                                        <span
                                                                            class="fst-italic">{{ $log->notes }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                {{-- Admin cập nhật trạng thái báo hỏng --}}
                                @auth
                                    @if (auth()->user()->hasRole('admin'))
                                        <form action="{{ route('admin.equipment-issues.update-status', $issue) }}"
                                            method="POST" class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                                            @csrf
                                            @method('PATCH')

                                            <div class="mb-1">
                                                <label class="form-label small mb-1">Trạng thái</label>
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="pending"
                                                        {{ $issue->status === 'pending' ? 'selected' : '' }}>Chờ xử
                                                        lý</option>
                                                    <option value="in_progress"
                                                        {{ $issue->status === 'in_progress' ? 'selected' : '' }}>
                                                        Đang xử lý</option>
                                                    <option value="resolved"
                                                        {{ $issue->status === 'resolved' ? 'selected' : '' }}>Đã xử
                                                        lý</option>
                                                    <option value="closed"
                                                        {{ $issue->status === 'closed' ? 'selected' : '' }}>Đã đóng
                                                    </option>
                                                </select>
                                            </div>



                                            <div class="mb-1">
                                                <label class="form-label small mb-1">Ghi chú xử lý</label>
                                                <textarea name="resolution_notes" class="form-control form-control-sm" rows="2" placeholder="Ghi chú xử lý...">{{ old('resolution_notes', $issue->resolution_notes) }}</textarea>
                                            </div>


                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                Cập nhật
                                            </button>
                                            {{-- <button type="submit" form="delete-issue-{{ $issue->id }}"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn chắc chắn muốn xóa báo hỏng này?');">
                                                Xóa
                                            </button> --}}
                                        </form>
                                        {{-- Form delete riêng để dùng chung button ở trên --}}
                                        <form id="delete-issue-{{ $issue->id }}"
                                            action="{{ route('client.equipment.issues.destroy', ['issue' => $issue->id]) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>


            {{-- Phân trang --}}
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $issues->firstItem() }}–{{ $issues->lastItem() }}
                    trên tổng {{ $issues->total() }} báo hỏng
                </div>

                <div>
                    {{ $issues->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="mb-0 text-muted">
                Chưa có báo hỏng nào cho thiết bị này.
            </p>
        @endif
    </div>
</div>

{{-- Model tạo báo hỏng --}}
<div class="modal fade" id="createIssueModal" tabindex="-1" aria-labelledby="createIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('client.equipment.issues.store', ['equipment' => $equipmentId]) }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="createIssueModalLabel">Báo hỏng thiết bị #{{ $equipmentId }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Hiển thị lỗi validate --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết<span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                            rows="4" required>{{ old('description') }}</textarea>

                        @error('description')
                            <label id="error-description" class="validation-error-label text-danger small"
                                for="description">
                                {{ $message }}
                            </label>
                        @enderror

                    </div>



                    <div class="mb-3">
                        <label class="form-label">Ảnh minh họa (có thể chọn nhiều)</label>
                        <input type="file" name="images[]" id="images"
                            class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                            multiple>

                        <div class="form-text">
                            Giữ Ctrl hoặc Shift để chọn nhiều ảnh cùng lúc. Chấp nhận: JPG, JPEG, PNG, GIF, WEBP.
                            Tối đa 2MB mỗi ảnh.
                        </div>

                        @error('images')
                            <label id="error-images" class="validation-error-label text-danger small" for="images">
                                {{ $message }}
                            </label>
                        @enderror

                        @error('images.*')
                            <label id="error-images" class="validation-error-label text-danger small" for="images">
                                {{ $message }}
                            </label>
                        @enderror

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Gửi báo hỏng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('createIssueModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
    </script>
@endif

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Xem ảnh</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreviewModalImg" src="" class="img-fluid rounded" alt="Preview">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('imagePreviewModal');
        const imgEl = document.getElementById('imagePreviewModalImg');

        if (!modalEl || !imgEl) return;

        modalEl.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const full = trigger?.getAttribute('data-full');
            imgEl.src = full || '';
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            imgEl.src = '';
        });
    });
</script>
