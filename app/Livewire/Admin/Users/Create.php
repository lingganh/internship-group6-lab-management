<?php

namespace App\Livewire\Admin\Users;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\Role as ModelsRole;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    #[validate(as: 'Họ và tên')]
    public string $fullName='';

    #[validate(as: 'Email')]
    public string $email='';

    #[validate(as: 'Mã SV/GV')]
    public string $code='';

    #[validate(as:'Vai trò')]
    public string $roleName='Teacher';

    #[validate(as: 'Số điện thoại')]
    public string $phone='';

    #[validate(as: 'Bộ môn')]
    public $department='';

    #[validate(as: 'Ngày sinh')]
    public $dateOfBirdth;

    #[validate(as: 'Giới tính')]
    public string $gender='';

//    #[validate(as: 'Ảnh đại diện')]
//    public $avatar;

    #[validate(as: 'Mật khẩu')]
    public string $password='';

    public $setPassword=false;

    public function render()
    {
        $departments = Department::all();
        return view('livewire.admin.users.create',
            [
                'departments' => $departments,
                'roles' => Role::displayAll(),
            ]
        );
    }

    public function save(){
        $this->validate();
        $roleId = ModelsRole::where('name', $this->roleName)->first()->id;

        try {
            $user = User::create([
                'full_name' => $this->fullName,
                'email' => $this->email,
                'code' => $this->code,
                'role_id' => $roleId,
                'phone' => $this->phone??null,
                'department_id' => $this->department?:null,
                'date_of_birth' => $this->dateOfBirdth??null,
                'gender' => $this->gender??null,
                'password' => bcrypt($this->password),
                'status' => UserStatus::Approved->value ?? UserStatus::Pending->value,
                'email_verified_at' => now(),
            ]);
            session()->flash('success', 'Thêm mới người dùng thành công!');
            return redirect()->route('admin.users.index');
        }
        catch (\Exception $e) {
            Log::error('Error update user', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('alert', message: 'Thêm mới người dùng thất bại!', type: 'error');
        }
        return null;

    }

    public function rules()
    {
        return [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,',
            'code' => 'required|string|max:10|unique:users,code,',
            'roleName' => 'required|string|in:'.implode(',', array_keys(Role::displayAll())),
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]*$/',
            'department' => 'nullable|integer|exists:departments,id',
            'dateOfBirdth' => 'nullable|date|before:today|after:1900-01-01|date_format:Y-m-d',
            'gender' => 'nullable|string|in:Nam,Nữ',
//            'avatar' => 'nullable|image|max:2048',
            'password' => 'required|string|min:8|max:255',
        ];

    }

    public function updatedSetPassword()
    {
        if($this->setPassword) {
            $this->password = '@FITA-2015$';
            $this->resetErrorBag('password');
        } else {
            $this->password = '';
            $this->addError('password', 'Trường Mật khẩu không được bỏ trống.');

        }

    }
}
