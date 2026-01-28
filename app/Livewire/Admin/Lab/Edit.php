<?php

namespace App\Livewire\Admin\Lab;

use App\Models\Lab;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use WithFileUploads;

    public $lab;

    public $name;
    public $status;
    public $description;
    public $code;
//    public $image;
    public $location;
    public $capacity;

//    public $oldImage;

    public function mount()
    {
        $this->lab = Lab::firstOrFail();
        $this->code = $this->lab->code;
        $this->name = $this->lab->name;
        $this->status = $this->lab->status;
        $this->location = $this->lab->location;
        $this->capacity = $this->lab->capacity;
        $this->description = $this->lab->description;
//        $this->oldImage = $this->lab->image_url;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'status' => 'required',
        'description' => 'nullable',
        'location' => 'nullable|string|max:255',
        'capacity' => 'nullable',
//        'image' => 'nullable|image|max:2048'
    ];

    public function update()
    {
        $this->validate();
//        $path = $this->oldImage;
//        if ($this->image) {
//            if ($this->oldImage && Storage::disk('public')->exists($this->oldImage)) {
//               Storage::disk('public')->delete($this->oldImage);
//            }
//            $path = $this->image->store('labs', 'public');
//        }

        $this->lab->update([
            'name' => $this->name,
            'status' => $this->status,
            'description' => $this->description,
            'location' => $this->location,
            'capacity' => $this->capacity,
//            'image_url' => $path
        ]);

        session()->flash('success','Cập nhật phòng Lab thành công');

        return redirect()->route('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.admin.lab.edit')
            ->layout('components.layouts.admin-layout');
    }
}
