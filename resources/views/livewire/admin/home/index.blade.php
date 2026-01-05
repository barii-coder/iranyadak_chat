<div class="w-full" style="margin:10px" wire:poll.1s>

    {{-- Header --}}
    <div class="mb-4 flex justify-between items-center m-5" dir="rtl">
        <h1 class="text-2xl font-bold text-gray-700">پنل پیام‌های ادمین‌ها</h1>
        <div class="flex gap-3">
            <button class="bg-white p-2 rounded-xl shadow">📩</button>
            <a href="/login" class="bg-white p-2 rounded-xl shadow">👤</a>
        </div>
    </div>

    {{-- Error --}}
    @error('prices')
    <div class="w-full max-w-md mx-auto">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 text-center rounded relative">
            <strong class="font-bold">خطا! </strong>
            <span>{{ $message }}</span>
        </div>
    </div>
    @enderror

    {{-- Chat In Progress --}}
    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[24%] max-h-[800px] overflow-auto">
        <div class="bg-blue-600 text-white p-4 rounded-t-2xl font-bold text-center sticky top-0 z-10">
            چت‌های در جریان
        </div>

        <ul class="space-y-2 text-sm">
            @foreach($messages as $message)
                <li id="message-{{ $message->id }}"
                    class="p-2 rounded-lg">

                    <div class="inline-block">
                        <img width="30px" class="rounded-2xl inline-block"
                             src="{{$message->user->profile_image_path}}" alt="">
                        <p class="inline-block text-lg m-1 cursor-pointer"
                           onclick="copyText(this)">
                            {{ $message->code }}
                        </p>

                        {{-- 🔴 فقط این قسمت تغییر کرده --}}
                        @foreach(['a','k','h','x','L','g'] as $c)
                            @php $key = $message->id . ':' . $c; @endphp

                            <button
                                onclick="handleCodeClick(event,'{{ $c }}',{{ $message->id }})"
                                class="px-3 py-1 rounded transition
                                {{ in_array($key,$selectedCodes)
                                    ? 'bg-green-600 text-white'
                                    : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                {{ $c }}
                            </button>
                        @endforeach
                    </div>

                    {{-- دکمه ثبت انتخاب‌ها --}}
                    @if(collect($selectedCodes)->contains(fn($v)=>str_starts_with($v,$message->id.':')))
                        <button
                            wire:click="submitSelectedCodes({{ $message->id }})"
                            class="mt-2 w-full bg-green-700 text-white py-1 rounded-xl">
                            ثبت انتخاب‌ها
                        </button>
                    @endif

                    <div class="bg-white mt-3 rounded-lg h-10 relative overflow-hidden">
                        <input type="text"
                               class="h-full w-full pl-2"
                               wire:model.defer="prices.{{ $message->id }}"
                               placeholder="قیمت"
                               wire:keydown.enter="submit_answer({{ $message->id }})">

                        <button
                            wire:click="submit_answer({{ $message->id }})"
                            class="absolute right-0 top-0 h-full px-4 bg-blue-600 text-white">
                            ➤
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>


    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[24%] max-h-[800px] overflow-auto">
        <div class="bg-blue-600 text-white p-4 rounded-t-2xl font-bold text-center sticky top-0">
            منتظر بررسی
        </div>

        <ul class="space-y-2 text-sm">
            @foreach($answers as $answer)
                <li class="p-2 rounded-lg" wire:key="answer-{{ $answer->id }}">

                    <div class="relative inline-block parent_of_back_icon">
                        <img width="30px" class="rounded-2xl inline-block"
                             src="{{$answer->message->user->profile_image_path}}" alt="">
                        <button class="inline-block middle back_icon mr-1"
                                onclick="hideMessage({{ $answer->message->id }})"
                                wire:click="back({{$answer->message->id}})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none">
                                <path d="M20 12H4M10 6l-6 6 6 6" stroke="currentColor" stroke-width="2"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <span
                        class="inline-block px-4 py-2 bg-slate-400  text-white rounded-xl cursor-pointer">
                        <p onclick="copyText(this)" class="p-0 inline-block text-center">
                        {{ $answer->message->code }}
                        </p>
                    </span>

                    @if($answer->price == 'x' or $answer->price == 'L')

                    @else
                        @if($answer->respondent_by_code == 1)
                            <span class="inline-flex px-4 py-2 bg-blue-500 text-white rounded-full">
                        {{ $answer->price }}
                    </span>
                        @elseif($answer->respondent_by_code == 0)
                            <span class="inline-flex px-4 py-2 bg-green-500 text-white rounded-full">
                        {{ $answer->price }}
                    </span>
                        @else
                            <span class="inline-flex px-4 py-2 bg-green-500 text-white rounded-full">
                        {{ $answer->price }}
                    </span>
                        @endif
                    @endif


                    @if($answer->respondent_by_code)
                        @if($answer->respondent_id)
                            @if($user->id == $answer->respondent_id)
                                <button wire:click="save_for_ad_price({{ $answer->message->id }})"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-xl float-right">
                                    ➜
                                </button>
                                <button wire:click="its_unavailable_on_column_2({{ $answer->message->id }})"
                                        class="px-3 py-2 mx-1 bg-red-600 text-white rounded-xl float-right">
                                    X
                                </button>
                            @endif
                            <span class=" m-1 text-white rounded-xl float-right">
                                <img width="30px" class="rounded-2xl" src="{{$answer->respondent_profile_image_path}}"
                                     alt="">
                            </span>
                        @else
                            @if($answer->price == 'x')
                                @if($user->id == $answer->user_id)
                                    <button
                                        wire:click="check_answer({{$answer->message->id }})"
                                        class="px-4 py-2 rounded-xl float-right bg-blue-600 text-white cursor-pointer z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                @endif
                                <span class="px-3 py-2 rounded-xl float-right mx-1 bg-red-600 text-white">
                                    محصول نا موجود
                                </span>
                            @elseif($answer->price === 'n')
                                @if($user->id == $answer->user_id)
                                    <button
                                        wire:click="check_answer({{$answer->message->id }})"
                                        class="px-4 py-2 rounded-xl float-right bg-blue-600 text-white cursor-pointer z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                @endif
                                <span class="px-3 py-2 rounded-xl float-right mx-1 bg-red-600 text-white">
                                    خوب نیست
                                </span>
                            @elseif($answer->price === 'L')
                                @if($user->id == $answer->user_id)
                                    <button
                                        wire:click="check_answer({{$answer->message->id }})"
                                        class="px-4 py-2 rounded-xl float-right bg-blue-600 text-white cursor-pointer z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                @endif
                                <span class="px-3 text-xs py-2 rounded-xl float-right mx-1 bg-green-500 text-white">
                                    آخرین قیمت سیستم رو بدید
                                </span>
                            @else
                                <button wire:click="i_had_it({{ $answer->message->id }})"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-xl float-right">
                                    من برداشتم
                                </button>
                            @endif
                        @endif
                    @else
                        @if($user->id == $answer->message->user_id)
                            <button wire:click="check_answer({{ $answer->message->id }})"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-xl float-right">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </button>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Waiting Price --}}
    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[24%] max-h-[800px] overflow-auto">
        <div class="bg-blue-600 text-white p-4 rounded-t-2xl font-bold text-center sticky top-0 z-10">
            منتظر قیمت
        </div>

        <ul class="space-y-2 text-sm">
            @foreach($wait_for_price as $message)
                <li class="p-2 rounded-lg" wire:key="wait-{{ $message->id }}">
                    <img width="30px" class="rounded-2xl inline-block" src="{{$message->user->profile_image_path}}"
                         alt="">
                    <span onclick="copyText(this)" class="cursor-pointer">{{ $message->code }}</span>
                    <span class="cursor-pointer text-red-600">{{ $message->text }}</span>
                    <button class="p-1 px-3 rounded-xl float-right bg-red-600 text-white cursor-pointer"
                            wire:click="price_is_unavailable({{$message->id}})">
                        X
                    </button>
                    <button class="p-1 px-3 mx-2 rounded-xl float-right bg-green-500 text-white cursor-pointer"
                            wire:click="code_answer_update('g',{{$message->id}})">
                        G
                    </button>
                    <button class="p-1 rounded-xl float-right text-white cursor-pointer"
                            wire:click="code_answer_update('n',{{$message->id}})">
                        🥲
                    </button>

                    <div class="bg-white mt-3 rounded-lg h-10 relative overflow-hidden">
                        <input type="text"
                               class="h-full w-full pl-2"
                               wire:model.defer="prices.{{ $message->id }}"
                               wire:keydown.enter="submit_answer({{ $message->id }})"
                               placeholder="قیمت">

                        <button wire:click="submit_answer({{ $message->id }})"
                                class="absolute right-0 top-0 h-full px-4 bg-blue-600 text-white">
                            ➤
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Finished --}}
    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[24%] max-h-[500px] overflow-auto">
        <div class="bg-blue-600 text-white p-4 rounded-t-2xl font-bold text-center sticky top-0">
            تکمیل شده
        </div>

        <ul class="space-y-2 text-sm">
            @foreach($ended_chats as $chat)
                <li class="p-2 rounded-lg">
                    <img class="w-10 rounded-full inline-block" src="{{$chat->user->profile_image_path}}">
                    <span>{{ $chat->code }}</span>
                    |
                    <span>{{ $chat->final_price }}</span>
                    <span class="float-right">
    {{ \Carbon\Carbon::parse($chat->updated_at)->timezone('Asia/Tehran')->format('H:i') }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

    <form wire:submit.prevent="submit" id="chat-box">
        <div id="chat-header">ارسال پیام</div>

        <div id="chat-body">
            <div class="msg bot">.کد محصول مورد نظر را وارد بنمایید</div>
        </div>

        <div id="chat-input">
            <input type="text" wire:model.defer="test" id="messageInput" placeholder="پیام..."/>
            <button onclick="sendMessage()">➤</button>
        </div>
    </form>

    <script>
        function handleCodeClick(event, code, messageId) {
            if (event.ctrlKey) {
                event.preventDefault();
                Livewire.dispatch('toggleCode', {
                    code: code,
                    messageId: messageId
                });
            } else {
                Livewire.dispatch('codeAnswerDirect', {
                    chat_code: code,
                    id: messageId
                });
            }
        }


        const chatBox = document.getElementById("chat-box");
        const chatBody = document.getElementById("chat-body");
        const input = document.getElementById("messageInput");
        const productCodeElement = document.getElementById("productCode");

        function hideMessage(id) {
            const el = document.getElementById('message-' + id);
            if (!el) return;

            el.classList.add('animate__fadeOut');

            setTimeout(() => {
                el.style.display = 'none';
            }, 500);
        }


        function sendMessage() {
            if (input.value.trim() === "") return;

            const userMsg = document.createElement("div");
            userMsg.className = "msg user";
            userMsg.innerText = input.value;
            chatBody.appendChild(userMsg);

            setTimeout(() => {
                const botMsg = document.createElement("div");
                botMsg.className = "msg bot";
                botMsg.innerText = "کد محصول ثبت شد☑️";
                chatBody.appendChild(botMsg);
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 600);

            input.value = "";
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function copyText(element) {
            const text = element.innerText;
            navigator.clipboard.writeText(text)
                .then(() => {
                    alert("کد کپی شد ✅");
                })
                .catch(err => {
                    console.error("خطا در کپی:", err);
                });
        }

    </script>

</div>
