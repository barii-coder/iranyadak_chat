<div class="flex gap-2 mb-4">
    <div class="flex gap-2 mb-4">
        @foreach($messages->unique('user_id') as $message)
            <button onclick="showUserMessages({{ $message->user_id }})"
                    class="focus:outline-none">
                <img class="w-[40px] h-[40px] rounded-full border"
                     src="{{ $message->user->profile_image_path }}"
                     alt="">
            </button>
        @endforeach
    </div>

    <div id="chat-container"
         class="h-[400px] overflow-y-auto bg-white border rounded p-3 space-y-4">

        @foreach($messages->groupBy('group_id') as $groupId => $group)
            @if($group->count() > 1)
                {{-- باکس برای پیام‌های گروهی --}}
                <div class="rounded-lg p-2 border bg-gray-100">
                    @foreach($group as $message)
                        <div class="message-item hidden mb-2" data-user-id="{{ $message->user_id }}">
                            <div class="max-w-[70%] {{ $message->user_id === auth()->id() ? 'ml-auto bg-blue-100' : 'bg-gray-100' }} p-2 rounded-lg">
                                <div class="text-sm">
                                    {{ $message->code }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- پیام تکی بدون باکس --}}
                @foreach($group as $message)
                    <div class="message-item hidden mb-2" data-user-id="{{ $message->user_id }}">
                        <div class="max-w-[70%] {{ $message->user_id === auth()->id() ? 'ml-auto bg-blue-100' : 'bg-gray-100' }} p-2 rounded-lg">
                            <div class="text-sm">
                                {{ $message->code }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach

    </div>

    <script>
        function showUserMessages(userId) {
            const messages = document.querySelectorAll('.message-item');
            const container = document.getElementById('chat-container');

            messages.forEach(el => {
                el.classList.add('hidden');
                if (el.dataset.userId == userId) {
                    el.classList.remove('hidden');
                }
            });

            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 50);
        }
    </script>
</div>
