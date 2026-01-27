<?php

namespace App\Livewire\Admin\Home;

use App\Models\Answer;
use App\Models\Message;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function PHPUnit\Framework\isArray;
use Carbon\Carbon;

class Index extends Component
{
    // chat_in_progress
    // ENDED = 0;
    // ANSWERED = 1;
    // NEW = 2;
    // WAIT_FOR_PRICE = 3;

    public $messageTimesByCode = [];

    public $test;
    public $prices = [];
    public array $selectedCodes = [];

    // 🔹 آرایه جدید برای کامنت‌ها
    public array $comments = [];

    protected $rules = [
        'prices' => 'required|array|min:1',
        'prices.*' => 'required|string|min:0',
    ];

    protected $messages = [
        'prices.required' => 'حداقل یک قیمت وارد کنید',
        'prices.array' => 'فرمت قیمت‌ها نامعتبر است',
        'prices.min' => 'حداقل یک قیمت وارد کنید',
    ];

    public $existingCodes = [];

//    protected $listeners = [
//        'toggleCode' => 'toggleCode',
//        'codeAnswerDirect' => 'code_answer',
//        'codeAnswerWithComment' => 'codeAnswerWithComment', // متد جدید
//    ];

    protected $listeners = [
        'checkNewMessages' => '$refresh',
        'toggleCode' => 'toggleCode',
        'codeAnswerDirect' => 'code_answer',
        'codeAnswerWithComment' => 'codeAnswerWithComment',
    ];


    public function checkNewMessages()
    {
        $this->dispatch('$refresh');
    }

    public $messageCounts = [];


    public function mount()
    {
        if (!Auth::check()) {
            abort(403);
        }

        // محاسبه تعداد تکرار هر کد در دیتابیس
        $repeatedCodes = \App\Models\Message::pluck('code')->toArray();

        $this->messageCounts = array_count_values($repeatedCodes);
    }

    public function submit()
    {
        $user = Auth::user();

        $lines = explode("\n", $this->test);

        // حذف خط‌های خالی (حتی اگه فقط اینتر یا فاصله باشه)
        $lines = array_filter($lines, function ($line) {
            return trim($line) !== '';
        });

        // اگه بعد از فیلتر چیزی نموند، کلاً ثبت نکن
        if (count($lines) === 0) {
            return;
        }

        $code = time() . '-' . rand(100000, 999999);

        foreach ($lines as $line) {
            Message::create([
                'user_id' => $user->id,
                'code' => trim($line),
                'active_group' => 1,
                'group_id' => $code,
                'chat_in_progress' => '2',
            ]);
        }

        $this->messageCounts = array_count_values(Message::pluck('code')->toArray());

        $this->test = ''; // پاک کردن textarea
    }

    public function submit_comment($message_id)
    {
        $user = Auth::user();
        Answer::query()->updateOrCreate(
            [
                'message_id' => $message_id
            ], [
                'user_id' => $user->id,
            'price'=>'',
            'comment' => $this->comments[$message_id],
            'message_id' => $message_id,
        ]);
        Message::query()->where('id', $message_id)->update(
            [
                'chat_in_progress' => '1',
                'active_group' => '0',
            ]
        );
        $this->comments[$message_id] = null;
    }


    public function submit_answer($id)
    {
        $this->validate();

        $a = Answer::query()->where('message_id', $id)->get();

        if ($a->isEmpty()) {
            Answer::query()->create([
                'user_id' => '1',
                'message_id' => $id,
                'price' => $this->prices[$id] ?? null,
            ]);

            Message::query()->where('id', $id)
                ->update(['chat_in_progress' => '1','active_group' => '0']);

            $this->prices = [];
        } else {
            Answer::query()->where('message_id', $id)->update([
                'price' => $this->prices[$id] ?? null,
                'respondent_by_code' => '',
            ]);

            Message::query()->where('id', $id)
                ->update(['chat_in_progress' => '1','active_group' => '0']);

            $this->prices = [];
        }
    }

    public function toggleCode($code, $messageId)
    {
        $key = $messageId . ':' . $code;

        if (in_array($key, $this->selectedCodes)) {
            $this->selectedCodes = array_values(
                array_diff($this->selectedCodes, [$key])
            );
        } else {
            $this->selectedCodes[] = $key;
        }
    }

    public function submitSelectedCodes($messageId)
    {
        $user = Auth::user();

        // فقط کدهای مربوط به همین پیام
        $codes = [];

        foreach ($this->selectedCodes as $item) {
            [$msgId, $code] = explode(':', $item);

            if ($msgId == $messageId) {
                $codes[] = $code;
            }
        }

        if (count($codes) === 0) {
            return;
        }

        $finalPrice = implode('-', $codes);
        $comment = $this->comments[$messageId] ?? null;

        Answer::query()->updateOrCreate(
            ['message_id' => $messageId],
            [
                'user_id' => $user->id,
                'price' => $finalPrice,
                'comment' => $comment,
                'respondent_by_code' => '1',
            ]
        );

        Message::query()->where('id', $messageId)
            ->update(['chat_in_progress' => '1','active_group' => '1']);

        // پاک کردن انتخاب‌ها و کامنت
        $this->selectedCodes = [];
        $this->comments[$messageId] = null;
    }

    public function deleteGroup($group_id)
    {
        Message::query()->where('group_id', $group_id)->delete();
    }


    // 🔹 متد جدید برای ذخیره دکمه + کامنت
    public function codeAnswerWithComment($chat_code, $messageId)
    {
        $user = Auth::user();

        // بررسی اینکه فقط وقتی کد حروف است، کامنت ثبت شود
        $comment = null;
        if (!is_numeric($chat_code)) {
            $comment = $this->comments[$messageId] ?? null;
        }

        Answer::query()->updateOrCreate(
            ['message_id' => $messageId],
            [
                'user_id' => $user->id,
                'price' => $chat_code,
                'comment' => $comment,
                'respondent_by_code' => '1',
            ]
        );

        Message::query()->where('id', $messageId)
            ->update(['chat_in_progress' => '1','active_group' => '1']);

        // پاک کردن input کامنت فقط وقتی ثبت شد
        if ($comment) {
            $this->comments[$messageId] = null;
        }
    }


    // بقیه متدها همونطوری که بود
    public function save_for_ad_price($messageId)
    {
        Message::query()->where('id', $messageId)->update([
            'chat_in_progress' => '3',
            'text' => null,
        ]);
    }

    public function check_answer($id)
    {
        $answer = Answer::query()->where('message_id', $id)->first();

        Message::query()->where('id', $id)->update([
            'chat_in_progress' => '0',
            'active_group' => '0',
            'final_price' => $answer->price,
            'updated_at' => now(),
        ]);
    }

    public function code_answer($chat_code, $id)
    {
        $user = Auth::user();

        $comment = $this->comments[$id] ?? null;

        Answer::query()->updateOrCreate(
            [
                'message_id' => $id,
            ],
            [
                'user_id' => $user->id,
                'message_id' => $id,
                'price' => $chat_code,
                'comment' => $comment,
                'respondent_by_code' => '1',
            ]);

        Message::query()->where('id', $id)
            ->update(['chat_in_progress' => '1', 'active_group' => '0']);
    }

    public function code_answer_update($chat_code, $id)
    {
        if ($chat_code == 'n') {
            $chat_code = 'نیاز به برسی دوباره';
        }

        Answer::query()->where('message_id', $id)->update([
            'price' => $chat_code,
            'respondent_by_code' => '1',
        ]);

        Message::query()->where('id', $id)
            ->update(['chat_in_progress' => '1','active_group' => '0']);
    }

    public function i_had_it($messageId)
    {
        $answer = Answer::where('message_id', $messageId)->first();
        $user = Auth::user();

        $answer->update([
            'respondent_profile_image_path' => $user->profile_image_path,
            'respondent_id' => $user->id,
        ]);

        Message::query()->where('id', $messageId)
            ->update([
                'chat_in_progress' => '1',
            ]);
    }

    public function back($group_id)
    {
        Message::query()->where('group_id', $group_id)
            ->update([
                'chat_in_progress' => '2',
                'active_group' => '1',
            ]);
    }

    public function delete_message($messageId)
    {
        Answer::query()->where('message_id', $messageId)->delete();
        Message::query()->where('id', $messageId)->delete();
    }

    public function price_is_unavailable($messageId)
    {
        $answer = Answer::query()->where('message_id', $messageId)->first();

        $answer->update([
            'price' => 'قیمت موجود نیست',
            'respondent_by_code' => '0',
        ]);

        Message::query()->where('id', $messageId)
            ->update(['chat_in_progress' => '1','active_group' => '0']);
    }

    public function its_unavailable_on_column_2($messageId)
    {
        Message::query()->where('id', $messageId)->update([
            'chat_in_progress' => '3',
            'text' => 'قیمت موجود نمیباشد',
        ]);
    }

    public function editPriceOnSoraats($formData, $group_id)
    {
        $user = Auth::user();
        foreach ($formData as $key => $value) {

            if (!str_starts_with($key, 'price.')) {
                continue;
            }

            $messageId = str_replace('price.', '', $key);

            $price = trim($value);

            if ($price === '') {
                continue;
            }

            Answer::query()->updateOrCreate(
                ['message_id' => $messageId],
                [
                    'user_id' => $user->id,
                    'price' => $price,
                    'respondent_by_code' => 0, // چون دستی وارد شده
                ]
            );

            Message::where('id', $messageId)->update([
                'chat_in_progress' => 1,
                'active_group' => '0',
                'final_price' => $price,
            ]);
        }

        Message::where('group_id', $group_id)->update([
            'chat_in_progress' => 1,
            'active_group' => '0',
        ]);
    }

    public function checkAll($group_id)
    {
        $messages = Message::query()->where('group_id', $group_id)->update(['chat_in_progress' => '0', 'active_group' => '0']);
    }


//    public function render()
//    {
//        $messages = Message::query()
//            ->where('chat_in_progress', '2')
//            ->orderBy('created_at', 'desc')
//            ->get();
//
//        $groups = Message::query()
//            ->where('chat_in_progress', '2')
//            ->orderBy('created_at', 'desc')
//            ->get()
//            ->groupBy('group_id');
//
//        $activeGroupIds = Message::query()
//            ->where('active_group', 1)
//            ->pluck('group_id')
//            ->unique();
//
//        $productsGrouped = Message::query()
//            ->whereIn('group_id', $activeGroupIds)
//            ->orderBy('created_at')  // یا هر فیلدی که میخوای ترتیب بده
//            ->get()
//            ->groupBy('group_id');
//
//        $wait_for_price = Message::query()
//            ->where('chat_in_progress', '3')
//            ->orderBy('updated_at', 'desc')
//            ->get();
//
//        $ended_chats = Message::query()
//            ->where('chat_in_progress', '0')
//            ->orderBy('updated_at', 'desc')
//            ->get();
//
//        $answers = Answer::query()
//            ->whereHas('message', function ($q) {
//                $q->where('chat_in_progress', '1');
//            })
//            ->orderBy('updated_at', 'desc')
//            ->get();
//
////        dd($answers);
//
//        $answersGrouped = $answers->groupBy(function ($answer) {
//            return $answer->message->group_id;
//        });
//
//        $this->messageCounts = Message::pluck('code')->toArray();
//        $this->messageCounts = array_count_values($this->messageCounts);
//
//        $user = Auth::user();
//
//        return view(
//            'livewire.admin.home.index',
//            compact('messages', 'ended_chats', 'answers', 'wait_for_price', 'user', 'productsGrouped', 'activeGroupIds', 'answersGrouped', 'groups')
//        );
//    }

    public function render()
    {
        $allMessages = Message::query()->orderBy('created_at', 'desc')->get();

        $messages = $allMessages->where('chat_in_progress', '2');

        $groups = $messages->groupBy('group_id');

        $activeGroupIds = $allMessages->where('active_group', 1)->pluck('group_id')->unique();

        $productsGrouped = $allMessages->whereIn('group_id', $activeGroupIds)
            ->sortByDesc('created_at')
            ->groupBy('group_id');

        $wait_for_price = $allMessages->where('chat_in_progress', '3')->sortByDesc('updated_at');

        $ended_chats = $allMessages->where('chat_in_progress', '0')->sortByDesc('updated_at');

        $answers = Answer::query()
            ->whereHas('message', fn($q) => $q->where('chat_in_progress', '1'))
            ->orderBy('updated_at', 'desc')
            ->get();

        $answersGrouped = $answers->groupBy(fn($answer) => $answer->message->group_id);

        $codeCounts = $allMessages->pluck('code')->countBy()->toArray();

        $lastTimes = $allMessages->groupBy('code')->map(fn($msgs) => $msgs->first()->created_at->diffForHumans())->toArray();

        $this->messageCounts = $codeCounts;
        $this->messageLastTimes = $lastTimes;

        $user = Auth::user();

        $now = now();

        $this->messageTimesByCode = Message::select('code', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('code')
            ->map(function ($items) use ($now) {
                return $items->map(function ($item) use ($now) {
                    $minutes = round($item->created_at->diffInMinutes($now));
                    if ($minutes < 60) {
                        return $minutes . 'm';
                    }

                    if ($minutes < 1440) {
                        return floor($minutes / 60) . 'h';
                    }

                    return floor($minutes / 1440) . 'd';
                })->toArray();
            })
            ->toArray();

        $groupReadyForCheck = Message::select('group_id')
            ->selectRaw('SUM(active_group) as active_count')
            ->groupBy('group_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->group_id => $item->active_count == 0
                ];
            })
            ->toArray();


        return view('livewire.admin.home.index', compact(
            'messages',
            'ended_chats',
            'answers',
            'wait_for_price',
            'user',
            'productsGrouped',
            'activeGroupIds',
            'answersGrouped',
            'groups',
            'groupReadyForCheck'
        ));
    }

}
