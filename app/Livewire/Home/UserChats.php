<?php

namespace App\Livewire\Home;

use App\Models\Message;
use Livewire\Component;

class UserChats extends Component
{
    public function render()
    {
        // گرفتن پیام‌ها
        $messages = Message::with('user')
            ->latest()
            ->take(200)
            ->get()
            ->reverse()
            ->values(); // ریست index

        // تشخیص پیام‌های گروهی (unique_id تکراری)
        $groupedIds = $messages
            ->groupBy('group_id')
            ->filter(fn ($group) => $group->count() > 1)
            ->keys()
            ->toArray();

        return view('livewire.home.user-chats', [
            'messages'   => $messages,
            'groupedIds' => $groupedIds,
        ]);
    }
}
