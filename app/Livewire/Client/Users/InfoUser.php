<?php

namespace App\Livewire\Client\Users;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InfoUser extends Component
{
    protected $listeners = [
        'confirmUpdateUser' => 'UpdateUser'
    ];


    #[validate(as: 'Họ và tên')]
    public string $fullName='';

    #[validate(as: 'Email')]
    public string $email='';

    public string $code='';

    public string $roleId='';

    #[validate(as: 'Số điện thoại')]
    public string $phone='';

    #[validate(as: 'Lớp')]
    public string $className='';

    #[validate(as: 'Ngày sinh')]
    public $dateOfBirdth;

    #[validate(as: 'Giới tính')]
    public string $gender='';

    public function render()
    {
        return view('livewire.client.users.info-user');
    }
    public function mount()
    {
        $user = auth()->user();
        $this->fullName = $user->full_name;
        $this->email = $user->email;
        $this->code = $user->code;
        $this->roleId = $user->role_id;
        $this->phone = $user->phone??'';
        $this->className = $user->class_name??'';
        $this->dateOfBirdth = $user->date_of_birth??'';
        $this->gender = $user->gender??'';
    }

    public function rules()
    {
        return [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9+\-\s()]*$/',
            'className' => 'nullable|string|max:100',
            'dateOfBirdth' => 'nullable|date|before:today|after:1900-01-01|date_format:Y-m-d',
            'gender' => 'nullable|string|in:Nam,Nữ',
        ];
    }

    public function openModel()
    {
        $this->validate();
        $this->dispatch('openModel', title:'Bạn có chắc chắn muốn cập nhật thông tin của bạn không?', type:'warning', confirmEvent:'confirmUpdateUser');
    }

    public function UpdateUser(){
        try {
            $user = auth()->user()->update([
                'full_name' => $this->fullName,
                'email' => $this->email,
                'phone' => $this->phone,
                'class_name' => $this->className,
                'date_of_birth' => $this->dateOfBirdth?:null,
                'gender' => $this->gender,
            ]);
            session()->flash('success', 'Cập nhật người dùng thành công!');
            return redirect()->route('home');
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
}
