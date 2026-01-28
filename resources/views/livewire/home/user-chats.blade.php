<div class="flex gap-2 p-1 mb-4">
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
         class="h-[400px] w-[300px] overflow-y-auto bg-white border rounded p-3 space-y-4">

        @foreach($messages->groupBy('group_id') as $groupId => $group)
            @if($group->count() > 1)
                {{-- باکس برای پیام‌های گروهی --}}
                <div class="rounded-lg p-2 border bg-gray-100 chat-group" data-group-id="{{ $groupId }}">
                    <button onclick="copyChatGroup('{{ $groupId }}', this)"
                            class="copy-btn p-1 rounded-full hover:bg-green-500/20 transition mb-2"
                            title="کپی پیام‌های این گروه">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#000" viewBox="0 0 24 24">
                            <path d="M16 1H4a2 2 0 0 0-2 2v12h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/>
                        </svg>
                    </button>

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

        function copyChatGroup(groupId, btn) {
            let lines = [];

            document.querySelectorAll(`.chat-group[data-group-id="${groupId}"] .message-item`).forEach(el => {
                const text = el.innerText.trim();
                if (text) lines.push(text);
            });

            if (lines.length === 0) return;

            navigator.clipboard.writeText(lines.join('\n'));
            showCopySuccess(btn);
        }


        function showCopySuccess(btn) {
            const svg = btn.querySelector('svg');
            if (!svg) return;

            const oldFill = svg.style.fill;
            svg.style.fill = '#16a34a';

            btn.classList.add('scale-110');

            setTimeout(() => {
                svg.style.fill = oldFill || '#000';
                btn.classList.remove('scale-110');
            }, 2000);
        }


    </script>
</div>
