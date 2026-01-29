<?php

namespace App\Http\Livewire\Admin\Home;

use Livewire\Component;
use Livewire\WithFileUploads;

class ChatBox extends Component
{
    use WithFileUploads;

    public $test;          // متن
    public $image;         // عکس پیست شده
    public $previewUrl;    // لینک preview عکس

    public function updatedImage()
    {
        if ($this->image) {
            $this->previewUrl = $this->image->temporaryUrl();
        }
    }

    public function submit()
    {
        $path = null;

        if ($this->image) {
            $path = $this->image->store('images', 'public');
        }

        // ذخیره در دیتابیس
        // MyModel::create([
        //     'text' => $this->test,
        //     'image_path' => $path,
        // ]);

        // ریست فرم
        $this->reset(['test', 'image', 'previewUrl']);
    }

    public function render()
    {
        return view('livewire.admin.home.chat-box');
    }
}
