<?php

namespace App\Livewire\Admin\Lab;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Lab;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;


class Create extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $name;
    public $code;
    public $status ='active';
    public $location;
    public $description;
    public $capacity;

    public $image;

    protected function rules()
    {
        return [
            'name'=>'required|string|max:255',
            'code'=>'required|string|max:10|unique:labs,code',
            'location'=>'required|string|max:255',
            'description'=>'nullable|string',
            'capacity'=>'required|integer',
            'status'=>'required|in:active,maintenance,locked',
            'image'=> 'nullable|image|max:2048',
        ];
    }

    public function save(){
        $this->validate();
        $path = null;
        if ($this->image) {
            $path = $this->image->store('lab_files','public');
        }


        Lab::create([
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'description' => $this->description ?? null,
            'image_url' => $path,
            'capacity' => $this->capacity,
            'location' => $this->location,
            'created_by'  => auth()->id(),
        ]);

        session()->flash('success', 'Thêm phòng Lab thành công');
        return redirect()->route('admin.lab.index');
    }
    protected $messages = [
        'name.required'        => 'Tên phòng không được bỏ trống.',
        'code.required'        => 'Mã phòng không được bỏ trống.',
        'location.required'    => 'Địa điểm không được bỏ trống.',
        'capacity.required'    => 'Sức chứa không được bỏ trống.',
    ];

    public function render()
    {
        return view('livewire.admin.lab.create')
            ->layout('components.layouts.admin-layout');
    }
}
