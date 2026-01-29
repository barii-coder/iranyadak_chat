<div>
    <form wire:submit.prevent="submit" id="chat-box" class="relative">
    <div id="chat-body" class="mb-2">
        <div class="msg bot">.کد محصول مورد نظر را وارد بنمایید</div>

        {{-- پیش نمایش عکس --}}
        @if($previewUrl)
            <img src="{{ $previewUrl }}" style="max-width: 150px; border-radius: 6px; margin-top: 5px;">
        @endif
    </div>

    <div id="chat-input" class="flex gap-2">
        <textarea
            wire:model.defer="test"
            id="messageInput"
            wire:keydown.enter.prevent="submit"
            placeholder="پیام..."
            class="flex-1 border rounded p-1"
        ></textarea>

        {{-- آپلود عکس مخفی --}}
        <input type="file" wire:model="image" class="hidden" id="imageInput">

        {{-- دکمه پیست یا انتخاب عکس --}}
        <button type="button" onclick="document.getElementById('imageInput').click()"
                class="bg-blue-600 text-white px-3 rounded">📎</button>

        <button type="submit" class="bg-green-600 text-white px-3 rounded">➤</button>
    </div>
</form>
<script>
    document.getElementById('messageInput').addEventListener('paste', function(e) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let index in items) {
            const item = items[index];
            if (item.kind === 'file') {
                const file = item.getAsFile();
            @this.set('image', file); // Livewire property
            }
        }
    });
</script>
</div>
