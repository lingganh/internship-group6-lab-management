<?php

namespace App\Livewire\Admin\Lab;

use App\Models\Lab;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $lab;

    public $name;
    public $code;
    public $status;
    public $description;

    public $image;
    public $oldImage;

    public function mount($id)
    {
        $this->lab = Lab::findOrFail($id);

        $this->name = $this->lab->name;
        $this->code = $this->lab->code;
        $this->status = $this->lab->status;
        $this->description = $this->lab->description;

        $this->oldImage = $this->lab->image;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'status' => 'required',
        'description' => 'nullable',
        'image' => 'nullable|image|max:2048'
    ];

    public function update()
    {
        $this->validate();

        // nếu có upload ảnh mới
        if ($this->image) {
            $path = $this->image->store('labs', 'public');
        } else {
            $path = $this->oldImage;
        }

        $this->lab->update([
            'name' => $this->name,
            'status' => $this->status,
            'description' => $this->description,
            'image' => $path
        ]);

        session()->flash('success','Cập nhật phòng Lab thành công');

        return redirect()->route('admin.lab.index');
    }

    public function render()
    {
        return view('livewire.admin.lab.edit')
            ->layout('components.layouts.admin-layout');
    }
}
