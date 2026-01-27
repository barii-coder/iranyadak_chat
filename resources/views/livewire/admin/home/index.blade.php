<div class="w-full" style="margin: 0 10px">
    <div style="width: 1px;height: 1px;background: #f00"></div>

    {{-- Error --}}
    @error('prices')
    <div class="w-full max-w-md mx-auto">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 text-center rounded relative">
            <strong class="font-bold">خطا! </strong>
            <span>{{ $message }}</span>
        </div>
    </div>
    @enderror

    <div class="bg-gray-300 rounded-2xl float-left m-2 w-[26%] max-h-[640px] overflow-auto">

        {{-- هدر --}}
        <div class="bg-blue-600 text-white p-2 font-bold text-center sticky top-0 z-10 rounded-t-2xl">
            چت‌های در جریان
        </div>

        <ul class="text-[13px] space-y-2">

            @foreach($groups as $groupId => $messages)

                @php
                    $firstMessage = $messages->first();
                @endphp

                {{-- گروه --}}
                <div class="bg-white rounded-lg p-2 border border-gray-300">

                    {{-- هدر گروه --}}
                    <div class="inline-block float-left">
                        <img src="{{ $firstMessage->user->profile_image_path }}"
                             class="w-6 h-6 rounded-full">
                        <button
                            {{--                            onclick="hideMessage({{ $firstAnswer->message->id }})"--}}
                            wire:click="deleteGroup('{{ $groupId }}')"
                            class="p-1 rounded-full hover:bg-red-500/20 transition"
                            title="حذف گروه">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="red" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6"/>
                                <path d="M14 11v6"/>
                                <path d="M8 6V4h8v2"/>
                            </svg>
                        </button>
                    </div>

                    {{-- پیام‌ها --}}
                    <div class="w-[96%]">

                        @foreach($messages as $message)

                            @php
                                $isEmpty = preg_match('/:\s*-\s*$/', trim($message->code));
                                $hasNoColon = strpos($message->code, ':') === false;
                            @endphp

                            <li id="message-{{ $message->id }}"
                                class="px-10  border-b last:border-b-0 border-gray-200">

                                @php
                                    $count = $messageCounts[$message->code] ?? 0;
                                @endphp

                                <p onclick="copyText(this)"
                                   class="cursor-pointer text-gray-700 leading-tight inline-block">
                                    {{ $message->code }}

                                    @if($count > 1)
                                        {{--                                        <span class="text-red-500">*</span>--}}

                                        <span class="text-gray-400 text-[10px] ml-1">
            (
            {{ implode(' , ', $messageTimesByCode[$message->code] ?? []) }}
            )
        </span>
                                    @endif
                                </p>


                                @if($isEmpty == 1 or $hasNoColon == true)

                                    {{-- دکمه‌های کد --}}
                                    <div class="inline-block gap-1 mt-1 flex-wrap">
                                        @foreach(['a','k','h','g','x','L'] as $c)
                                            @php $key = $message->id . ':' . $c; @endphp
                                            <button
                                                onclick="handleCodeClick(event,'{{ $c }}',{{ $message->id }})"
                                                style="{{$c == 'x' ? 'margin-left:5px' : ''}}"
                                                class="px-2 rounded text-[12px]
                                            {{ in_array($key,$selectedCodes)
                                                ? 'bg-green-600 text-white'
                                                : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                                {{ $c }}
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- کامنت --}}
                                    <input type="text"
                                           wire:model.defer="comments.{{ $message->id }}"
                                           dir="rtl"
                                           wire:keydown.enter="submit_comment({{ $message->id }})"
                                           placeholder="کامنت"
                                           class="mt-1 w-[45%] float-right bg-gray-100 rounded px-1 py-0.5">
                                    {{-- قیمت --}}
                                    <div class="flex mt-1 bg-gray-100 rounded overflow-hidden">
                                        <input type="text"
                                               wire:model.lazy="prices.{{ $message->id }}"
                                               placeholder="قیمت"
                                               wire:keydown.enter="submit_answer({{ $message->id }})"
                                               class="flex-1 bg-transparent px-1 py-0.5">
                                        <button
                                            wire:click="submit_answer({{ $message->id }})"
                                            class="px-2 bg-blue-600 text-white text-[9px]">
                                            ➤
                                        </button>
                                    </div>
                                    {{-- ثبت انتخاب --}}
                                    @if(collect($selectedCodes)->contains(fn($v)=>str_starts_with($v,$message->id.':')))
                                        <button
                                            wire:click="submitSelectedCodes({{ $message->id }})"
                                            class="mt-1 w-full bg-green-600 text-white py-0.5 rounded">
                                            ثبت
                                        </button>
                                    @endif

                                @endif

                            </li>
                        @endforeach

                    </div>

                </div>
            @endforeach

        </ul>
    </div>

    <div
        class="bg-gray-300 rounded-2xl float-left m-2 w-[26%] max-h-[640px] overflow-auto shadow-lg border border-slate-200">
        <div class="bg-gradient-to-r  bg-blue-600 text-white p-2 font-bold text-center sticky top-0 z-10">
            منتظر بررسی
        </div>

        <ul class="text-sm p-1">

            @foreach($answersGrouped as $groupId => $groupAnswers)
                <li class=" rounded-2xl shadow-sm m-1 border border-slate-700">

                @php
                    $firstAnswer = $groupAnswers->first();
                @endphp

                <li class="bg-gray-100 rounded-2xl p-1 w-[100%] shadow-sm border border-slate-700 float-right">

                    <ul class="">

                        <div class="inline-block">
                            @if(
$user->id == $firstAnswer->message->user_id &&
($groupReadyForCheck[$groupId] ?? false)
)
                                <button
                                    onclick="hideMessage({{ $firstAnswer->message->id }})"
                                    wire:click="checkAll('{{ $groupId }}')"
                                    class="p-2 rounded-full hover:bg-red-500/20 transition"
                                    title="تایید کل">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24"
                                         fill="none" stroke="black" stroke-width="3"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                </button>
                            @endif
                            <img
                                src="{{ $firstAnswer->message->user->profile_image_path }}"
                                class="w-8 h-8 rounded-xl ring-2 ring-white shadow block"
                                alt=""
                            >
                            <button
                                {{--onclick="hideMessage({{ $firstAnswer->message->id }})"--}}
                                wire:click="back('{{ $groupId }}')"
                                class="p-1 rounded-full hover:bg-red-500/20 transition"
                                title="برگشت">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none">
                                    <path d="M20 12H4M10 6l-6 6 6 6"
                                          stroke="black" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="float-right w-[90%]">
                            @foreach($groupAnswers as $answer)
                                <li
                                    class="rounded-xl hover:bg-slate-100 transition-all duration-200 p-1"
                                    wire:key="answer-{{ $answer->id }}">
                                    <div class="inline-block mt-2">
                                <span
                                    class="inline-block  text-slate-500 rounded-2xl cursor-pointer ">
                                    <p onclick="copyText(this)" class="inline-block text-center text-xs font-semibold">
                                        {{ $answer->message->code }}
                                    </p>
                                </span>
                                        @if($answer->comment)
                                            <span
                                                class="inline-block ml-2 px-3 py-1 bg-amber-100 text-amber-800 rounded-xl text-xs shadow-sm">
                                        {{ $answer->comment }}
                                    </span>
                                        @endif
                                    </div>

                                    {{-- قیمت --}}
                                    @if($answer->price == 'x' or $answer->price == 'L')

                                    @else
                                        @if($answer->respondent_by_code == 1)
                                            <span
                                                class="inline-flex px-4 py-1.5 bg-blue-500 text-white rounded-full text-xs shadow">
                                        {{ $answer->price }}
                                    </span>
                                        @elseif($answer->respondent_by_code == 0)
                                            <span
                                                class="inline-flex px-4 py-1.5 bg-green-500 text-white rounded-full text-xs shadow float-right">
                                        {{ $answer->price }}
                                    </span>
                                        @else
                                            <span
                                                class="inline-flex px-4 py-1.5 bg-green-500 text-white rounded-full text-xs shadow">
                                        {{ $answer->price }}
                                    </span>
                                        @endif
                                    @endif

                                    {{-- دکمه‌ها (کاملاً دست‌نخورده) --}}
                                    @if($answer->respondent_by_code)
                                        @if($answer->respondent_id)
                                            <div class="float-right" style="margin: -3px">
                                                @if($user->id == $answer->respondent_id)
                                                    <button
                                                        wire:click="save_for_ad_price({{ $answer->message->id }})"
                                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl float-right shadow transition">
                                                        ➜
                                                    </button>

                                                    <button
                                                        wire:click="its_unavailable_on_column_2({{ $answer->message->id }})"
                                                        class="px-3 py-1.5 mx-1 bg-red-600 hover:bg-red-700 text-white rounded-xl float-right shadow transition">
                                                        X
                                                    </button>
                                                @endif

                                                <span class="m-1 float-right">
                                        <img width="30px"
                                             class="rounded-xl ring-2 ring-white shadow"
                                             src="{{$answer->respondent_profile_image_path}}">
                                    </span>
                                            </div>
                                        @else
                                            @if($answer->price == 'x')
                                                <div class="float-right">
                                            <span
                                                class="px-3 py-1.5 bg-red-500 text-white rounded-xl float-right text-xs shadow">
                                            محصول نا موجود
                                            </span></div>
                                            @elseif($answer->price === 'n')
                                                <div class="float-right">
                                                <span
                                                    class="px-3 py-1.5 bg-red-500 text-white rounded-xl float-right text-xs shadow">
                                            خوب نیست
                                        </span>
                                                </div>
                                            @elseif($answer->price === 'L')
                                                <div class="float-right">
                                            <span
                                                class="px-3 py-1.5 bg-emerald-500 text-white rounded-xl float-right text-xs shadow">
                                            آخرین قیمت سیستم رو بدید
                                        </span>
                                                </div>
                                            @else
                                                <div class="float-right">
                                                    <button
                                                        wire:click="i_had_it({{ $answer->message->id }})"
                                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl float-right shadow transition">
                                                        من برداشتم
                                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        {{--                                        <button--}}
                                        {{--                                            wire:click="check_answer({{ $answer->message->id }})"--}}
                                        {{--                                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl float-right shadow transition">--}}
                                        {{--                                            ✔--}}
                                        {{--                                        </button>--}}
                                    @endif

                                </li>
                            @endforeach
                        </div>
                    </ul>

                </li>

            @endforeach

        </ul>
    </div>


    {{-- Waiting Price --}}
    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[26%] max-h-[640px] overflow-auto">
        <div class="bg-blue-600 text-white p-2 rounded-t-2xl font-bold text-center sticky top-0 z-10">
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

    <div class="bg-gray-200 rounded-2xl float-left m-2 w-[18%] max-h-[640px] overflow-auto">

        <div
            class="bg-blue-600 text-white p-2 font-bold text-center sticky top-0 z-10 rounded-t-2xl">
            تکمیل شده
        </div>

        <ul class="text-xs space-y-2">

            @foreach($ended_chats->groupBy('group_id') as $groupId => $groupChats)

                @php
                    $firstChat = $groupChats->first();
                @endphp

                <li class="bg-white rounded-lg border border-gray-300 p-2">

                    {{-- هدر گروه --}}
                    <div class="flex items-center justify-between mb-1">

                        <div class="flex items-center gap-2">
                            <img
                                src="{{ $firstChat->user->profile_image_path }}"
                                class="w-6 h-6 rounded-lg"
                                alt="">
                            <span class="font-bold text-gray-700 text-xs">
                            گروه {{ $groupId }}
                        </span>
                        </div>

                        <span class="text-gray-500 text-[10px]">
                        {{ \Carbon\Carbon::parse($firstChat->updated_at)->timezone('Asia/Tehran')->format('H:i') }}
                    </span>
                    </div>

                    {{-- آیتم‌ها --}}
                    @foreach($groupChats as $chat)
                        <div class="border-t border-gray-200 pt-1 mt-1 leading-tight flex justify-between">

                        <span class="text-gray-700">
                            {{ $chat->code }}
                        </span>

                            <span class="font-bold text-gray-800">
                            {{ $chat->final_price }}
                        </span>

                        </div>
                    @endforeach

                </li>

            @endforeach
        </ul>
    </div>


    {{--    <div class="status_bar">--}}
    {{--        <div class="status_text sticky top-0">--}}
    {{--            صورت ها--}}
    {{--        </div>--}}
    {{--        <div class="m-2">--}}
    {{--            @foreach($productsGrouped as $groupId => $messages)--}}
    {{--                @if(count($messages)>1)--}}
    {{--                    <div class="border rounded p-4 mb-4 m-1 bg-gray-50 inline-block float-left"--}}
    {{--                         style="font-size: 9pt; font-weight: bold">--}}

    {{--                        --}}{{--<div class="font-bold mb-2">گروه: {{ $groupId }}</div>--}}
    {{--                        <form--}}
    {{--                            wire:submit.prevent="editPriceOnSoraats(Object.fromEntries(new FormData($event.target)),'{{$groupId}}')">--}}
    {{--                            @foreach($messages as $message)--}}
    {{--                                <div--}}
    {{--                                    class="{{ Str::endsWith(trim($message->code), ': -') ? 'text-gray-400 italic' : '' }}">--}}
    {{--                                        <?php--}}
    {{--                                        $msg = explode(":", $message->code);--}}
    {{--                                        $msg1 = $msg[0] . ':';--}}
    {{--                                        ?>--}}
    {{--                                    {{ $msg1 }}--}}
    {{--                                    @if($message->answers->last()?->respondent_by_code == 1 and $message->final_price == null)--}}
    {{--                                        <span class="text-green-600">--}}
    {{--                                    <input style='border: 1px solid #aaa!important' value='درحال برسی'--}}
    {{--                                           name='price.{{$message->id}}'>--}}
    {{--                                </span>--}}
    {{--                                    @endif--}}
    {{--                                    @if(Str::endsWith(trim($message->code), ['1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']) )--}}
    {{--                                            <?php--}}
    {{--                                            $a = explode(":", $message->code);--}}
    {{--                                            if (isset($a[1])) {--}}
    {{--                                                $b = "--}}
    {{--                                    <input style='border: 1px solid #aaa!important' value='$a[1]' name='price.$message->id'>--}}
    {{--                                    ";--}}
    {{--                                                $c = str_replace($a[1], $b, $a);--}}
    {{--                                                echo $c[1];--}}
    {{--                                            } else {--}}
    {{--                                                $d = "--}}
    {{--                                    <input style='border: 1px solid #aaa!important' name='price.$message->id'>--}}
    {{--                                            ";--}}
    {{--                                                echo $d;--}}
    {{--                                            }--}}
    {{--                                            ?>--}}
    {{--                                    @endif--}}
    {{--                                    @if($message->answers->last()?->price != null and $message->answers->last()?->respondent_by_code != 1)--}}
    {{--                                        <span class="text-green-600"--}}
    {{--                                              style="{{ Str::endsWith(trim($message->code), ': -') ? 'display: inline' : 'display: none' }}">--}}
    {{--                                        <input type="text" style="border: 1px solid #aaa!important"--}}
    {{--                                               value="{{ $message->answers->last()?->price }}"--}}
    {{--                                               name='price.{{$message->id}}'--}}
    {{--                                        >--}}
    {{--                                    </span>--}}
    {{--                                    @endif--}}
    {{--                                </div>--}}
    {{--                            @endforeach--}}
    {{--                            <button type="submit"--}}
    {{--                                    class="px-3 py-2 bg-blue-600 text-white rounded-xl float-right">--}}
    {{--                                ثبت همه--}}
    {{--                            </button>--}}
    {{--                        </form>--}}
    {{--                    </div>--}}
    {{--                @endif--}}
    {{--            @endforeach--}}

    {{--        </div>--}}
    {{--    </div>--}}

    <div class="status_bar">
        <div class="status_text sticky top-0">
            صورت ها
        </div>

        <div class="m-2">
            @foreach($productsGrouped as $groupId => $messages)
                @if(count($messages) > 1)

                    <div class="border rounded p-4 mb-4 m-1 bg-gray-50 inline-block float-left"
                         style="font-size: 9pt; font-weight: bold">

                        <form
                            wire:submit.prevent="editPriceOnSoraats(
                            Object.fromEntries(new FormData($event.target)),
                            '{{ $groupId }}'
                        )"
                        >

                            @foreach($messages as $message)

                                <div
                                    class="{{ Str::endsWith(trim($message->code), ': -') ? 'text-gray-400 italic' : '' }}">

                                    @php
                                        $parts = explode(':', $message->code);
                                        $label = $parts[0] . ':';
                                        $codeValue = trim($parts[1] ?? '');
                                    @endphp

                                    {{ $label }}

                                    @if(
                                        $message->answers->last()?->price !== null &&
                                        $message->answers->last()?->respondent_by_code != 1
                                    )
                                        {{-- قیمت ثبت‌شده --}}
                                        <input
                                            type="text"
                                            style="border: 1px solid #aaa!important"
                                            value="{{ $message->answers->last()->price }}"
                                            name="price.{{ $message->id }}"
                                        >

                                    @elseif(
                                        $message->answers->last()?->respondent_by_code == 1 &&
                                        $message->final_price == null
                                    )
                                        {{-- در حال بررسی --}}
                                        <input
                                            style="border: 1px solid #aaa!important; color: green;"
                                            placeholder="درحال بررسی"
                                            name="price.{{ $message->id }}"
                                        >

                                    @elseif(
                                        Str::endsWith(trim($message->code), [
                                            '1','2','3','4','5','6','7','8','9','0',
                                            'A','B','C','D','E','F','G','H','I','J',
                                            'K','L','M','N','O','P','Q','R','S','T',
                                            'U','V','W','X','Y','Z'
                                        ])
                                    )
                                        {{-- مقدار داخل code --}}
                                        <input
                                            style="border: 1px solid #aaa!important;"
                                            value="{{ $codeValue }}"
                                            name="price.{{ $message->id }}"
                                        >

                                    @else
                                        {{-- خالی --}}
                                        <input
                                            style="border: 1px solid #aaa!important"
                                            name="price.{{ $message->id }}"
                                        >
                                    @endif

                                </div>

                            @endforeach

                            <button
                                type="submit"
                                class="px-3 py-2 bg-blue-600 text-white rounded-xl float-right mt-2"
                            >
                                ثبت همه
                            </button>

                        </form>
                    </div>

                @endif
            @endforeach
        </div>
    </div>


    <form wire:submit.prevent="submit" id="chat-box">
        <div id="chat-header">
            <a href="/view-user-chats" class="bg-white p-1 rounded-xl shadow float-left">📩</a>
            <a href="/login" class="bg-white p-1 rounded-xl shadow float-left">👤</a>
            <span class="float-right">
            ارسال پیام
            </span>
        </div>

        <div id="chat-body">
            <div class="msg bot">.کد محصول مورد نظر را وارد بنمایید</div>
        </div>

        <div id="chat-input">
            <textarea type="text" wire:model.defer="test" id="messageInput" wire:keydown.enter.prevent="submit"
                      placeholder="پیام..."> </textarea>
            <button onclick="sendMessage()">➤</button>
        </div>
    </form>


    <script>

        // setInterval(() => {
        //     Livewire.dispatch('checkNewMessages')
        // }, 30);


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
                    element.style.color = '#0f0';
                    setTimeout(() => {
                        element.style.color = '';
                    }, 1000);
                })
                .catch(err => {
                    console.error("خطا در کپی:", err);
                });
        }

    </script>

</div>
