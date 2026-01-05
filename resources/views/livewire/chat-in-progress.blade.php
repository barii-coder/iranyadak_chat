<div class="bg-gray-200 border-l rounded-b-2xl float-left m-2 w-[24%] max-h-[800px]" style="overflow: auto">
    <div class="bg-blue-600 text-white p-4 rounded-t-2xl font-bold text-center z-10"
         style="position: sticky;top: 0">
        چت‌های در جریان
    </div>

    <ul class="space-y-2 text-sm overflow-scroll">
        @foreach($messages as $message)
            <li class="p-2 rounded-lg">
                <p class="text-right">
                    کاربر شماره {{$message->user_id}}
                </p>
                <img class="w-[40px] rounded-[100px] inline-block" src="/IMG/prof.jpg" alt="">
                <div id="productCode" class="inline-block">
                    <p class="inline-block">
                        {{$message->code}}
                    </p>
                    <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
                            wire:click="code_answer('a',{{ $message->id }})">a
                    </button>
                    <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
                            wire:click="code_answer('k',{{ $message->id }})">k
                    </button>
                    <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
                            wire:click="code_answer('h',{{ $message->id }})">h
                    </button>
                    <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition"
                            wire:click="code_answer('x',{{ $message->id }})">x
                    </button>
                </div>
                <div class="bg-white"
                     style="margin: 15px 5px 0 5px;padding: 0;border-radius: 10px;height: 40px;overflow: hidden;position:relative;">
                    <input type="text"
                           class="h-[40px] pl-1 w-full bg-white"
                           wire:model.defer="prices.{{$message->id}}"
                           wire:keydown.enter="submit_answer({{ $message->id }})">
                    <button type="submit"
                            wire:click="submit_answer({{ $message->id }})"
                            class="inline-block px-4 py-2 bg-blue-600 text-white"
                            style="position:absolute;right: 0;height: 100%">
                        ثبت
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
</div>
