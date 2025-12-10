<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\Role as ModelsRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    protected $listeners = [
        'confirmUpdateUser' => 'confirmUpdateUser',
        'delete' => 'delete',
        'confirmArchiveUsers' => 'archiveUsers',
        'confirmRearchiveUsers' => 'rearchiveUsers',
        'confirmApproveUser' => 'approveUser',
    ];

    public $userId;

    #[validate(as: 'Họ và tên')]
    public string $fullName='';

    #[validate(as: 'Email')]
    public string $email='';

    #[validate(as: 'Mã SV/GV')]
    public string $code='';

    #[validate(as:'Vai trò')]
    public string $roleName='';

    #[validate(as: 'Số điện thoại')]
    public string $phone='';

    #[validate(as: 'Bộ môn')]
    public $department='';

    #[validate(as: 'Ngày sinh')]
    public $dateOfBirdth;

    #[validate(as: 'Giới tính')]
    public string $gender='';

    public string $lastLoginAt='';

    public string $createUserAt='';

    public function render()
    {
        $user = User::find($this->userId);
        $departments = Department::all();
        return view('livewire.admin.users.edit',[
            'roles' => Role::displayAll(),
            'user' => $user,
            'departments' => $departments
        ]);
    }

    public function mount()
    {
        $user = User::find($this->userId);
        $this->fullName = $user->full_name;
        $this->email = $user->email;
        $this->code = $user->code;
        $this->roleName = $user->role_value??'';
        $this->phone = $user->phone??'';
        $this->department = $user->department_id??'';
        $this->dateOfBirdth = $user->date_of_birth??'';
        $this->gender = $user->gender??'';
        $this->lastLoginAt = $user->last_login_at ? Carbon::parse($user->last_login_at)->format('d/m/Y - H:i') : 'Chưa đăng nhập';
        $this->createUserAt = $user->created_at->format('d/m/Y');
    }

    public function rules()
    {
        return [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->userId,
            'code' => 'required|string|max:10|unique:users,code,'.$this->userId,
            'roleName' => 'required|string|in:'.implode(',', array_keys(Role::displayAll())),
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]*$/',
            'department' => 'nullable|integer|exists:departments,id',
            'dateOfBirdth' => 'nullable|date|before:today|after:1900-01-01|date_format:Y-m-d',
            'gender' => 'nullable|string|in:Nam,Nữ',
        ];

    }

    public function update()
    {
        $this->validate();
        $this->dispatch('openModel',title:'Bạn có chắc chắn muốn cập nhật thông tin người dùng này không?', type:'warning', confirmEvent:'confirmUpdateUser');
    }

    public function confirmUpdateUser()
    {
        $payloads = $this->getAttributeNotEmpty();
        try {
            $user = User::where('id', $this->userId)->update($payloads);
            session()->flash('success', 'Cập nhật người dùng thành công!');
            return redirect()->route('admin.users.index');
        }
        catch (\Exception $e) {
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', message: 'Cập nhật người dùng thất bại!', type: 'error');
        }
        return null;
    }

    private function getAttributeNotEmpty():array
    {
        $roleId = ModelsRole::where('name', $this->roleName)->first()->id;
        $attributes = [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'code' => $this->code,
            'role_id' => $roleId,
            'phone' => $this->phone,
            'department_id' => $this->department,
            'date_of_birth' => $this->dateOfBirdth,
            'gender' => $this->gender
        ];

        return collect($attributes)
            ->map(fn($value) => $value === '' ? null : $value)
            ->toArray();
    }

    public function openModelDelete()
    {
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn xóa người dùng này không?',confirmEvent:'delete' );
    }

    public function delete()
    {
        User::find($this->userId)->delete();
        session()->flash('success', 'Xóa người dùng thành công!');
        return redirect()->route('admin.users.index');
    }

    public function openApproveModal()
    {
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn duyệt tài khoản này không',confirmEvent:'confirmApproveUser' );
    }

    public function approveUser(){
        $user = User::find($this->userId);
        $user->status = UserStatus::Approved->value;
        $user->save();
        $this->dispatch('alert', type:'success', message:'Duyệt tài khoản thành công!');
    }

    public function openArchiveModal(){
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn lưu trữ người dùng này không?',confirmEvent:'confirmArchiveUsers' );
    }

    public function archiveUsers()
    {
        $user = User::find($this->userId);
        $user->status = UserStatus::Archived->value;
        $user->save();
        session()->flash('success', 'Lưu trữ tài khoản thành công!');
        return redirect()->route('admin.users.index');
    }

    public function openRearchiveModal(){
        $this->dispatch('openModel', type:'warning',title:'Bạn có chắc chắn muốn khôi phục người dùng này không?',confirmEvent:'confirmRearchiveUsers' );
    }

    public function rearchiveUsers()
    {
        $user = User::find($this->userId);
        $user->status = UserStatus::Approved->value;
        $user->save();
        $this->dispatch('alert', type:'success', message:'Khôi phục tài khoản thành công!');
    }
    public function changePassword()
    {

    }
}
