<div>
    <div wire:loading class="p-5 w-100 text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
    </div>
    @if($group)
        <div class="card-body" wire:loading.remove>
            <div class="row">
                <div class="col-6">
                    <label for="fullName" class="col-form-label">
                        Tên nhóm: {{$group->name??''}}
                    </label>
                </div>
                <div class="col-6">
                    <label for="role" class="col-form-label">
                        Số thành viên: {{$group->students()->count()??''}}
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="fullName" class="col-form-label">
                        Giáo viên hướng dẫn: {{$group->leader->full_name??''}}
                    </label>
                </div>
                <div class="col-6">
                    <label for="role" class="col-form-label">
                        Ngày tạo: {{$group->created_at ? $group->created_at->format('d-m-Y') : ''}}
                    </label>
                </div>
            </div>
            <div class="row">
                <label for="description" class="col-form-label">
                    Mô tả nhóm:
                </label>
                <div class="border p-3 rounded">
                    {{$group->description}}
                </div>
            </div>
            <div class="row">
                <label for="members" class="col-form-label mt-3">
                    Danh sách thành viên nhóm:
                </label>
                <div class="table-responsive-md">
                    <table class="table fs-table text-center">
                        <thead>
                        <tr class="table-light">
                            <th>STT</th>
                            <th>Họ và tên</th>
                            <th>Mã sinh viên</th>
                            <th>Lớp</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($group->students as $student)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$student->full_name}}</td>
                                <td>{{$student->code}}</td>
                                <td>{{$student->class_name}}</td>
                            </tr>
                        </tbody>
                        @empty
                            <x-table-empty :colspan="7"/>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
