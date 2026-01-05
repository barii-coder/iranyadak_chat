<?php

namespace App\Livewire\Admin\Home;

use App\Models\Answer;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{

    // chat_in_progress
    // ENDED = 0;
    //ANSWERED = 1;
    // NEW = 2;
    // WAIT_FOR_PRICE = 3;

    public $test;
    public $prices = [];
    protected $rules = ['prices' => 'required|array|min:1', 'prices.*' => 'required|string|min:0',];
    protected $messages = ['prices.required' => 'حداقل یک قیمت وارد کنید', 'prices.array' => 'فرمت قیمت‌ها نامعتبر است', 'prices.min' => 'حداقل یک قیمت وارد کنید',];

    public function mount()
    {
        if (!Auth::check()) {
            abort(403);
        }
    }

    public function submit()
    {
        $user = Auth::user();
        Message::query()->create(['user_id' => $user->id, 'code' => $this->test, 'chat_in_progress' => '2',]);
    }

    public function submit_answer($id)
    {
        $this->validate();
        $a = Answer::query()->where('message_id', $id)->get();;
        if ($a->isEmpty()) {
            Answer::query()->create(['user_id' => '1', 'message_id' => $id, 'price' => $this->prices[$id] ?? null,]);
            Message::query()->where('id', $id)->update(['chat_in_progress' => '1',]);
            $this->prices = [];
        } else {
            Answer::query()->where('message_id', $id)->update(['price' => $this->prices[$id] ?? null, 'respondent_by_code' => '', 'respondent_name' => '',]);
            Message::query()->where('id', $id)->update(['chat_in_progress' => '1',]);
            $this->prices = [];
        }
    }

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
        Message::query()->where('id', $id)->update(['chat_in_progress' => '0', 'final_price' => $answer->price,]);
    }

    public function code_answer($chat_code, $id)
    {
        $user = Auth::user();
        Answer::query()->create(['user_id' => $user->id, 'message_id' => $id, 'price' => $chat_code, 'respondent_by_code' => '1',]);
        Message::query()->where('id', $id)->update(['chat_in_progress' => '1',]);
    }

    public function code_answer_update($chat_code, $id)
    {
        if ($chat_code == 'n') {
            $chat_code = 'نیاز به برسی دوباره';
        }
        Answer::query()->where('message_id',$id)->update(['price' => $chat_code, 'respondent_by_code' => '1',]);
        Message::query()->where('id', $id)->update(['chat_in_progress' => '1',]);
    }

    public function i_had_it($messageId)
    {
        $answer = Answer::where('message_id', $messageId)->first();
        $user = Auth::user();
        $name = $user->name;
        $prof = $user->profile_image_path;
        $id = $user->id;
        $answer->update(['respondent_name' => $name, 'respondent_profile_image_path' => $prof, 'respondent_id' => $id,]);
        Message::query()->where('id', $messageId)->update(['chat_in_progress' => '1',]);
    }

    public function back($messageId)
    {
        Answer::query()->where('message_id', $messageId)->delete();
        Message::query()->where('id', $messageId)->update(['chat_in_progress' => '2',]);
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
            'respondent_by_code' => '0'
        ]);
        Message::query()->where('id', $messageId)->update(['chat_in_progress' => '1',]);
    }

    public function its_unavailable_on_column_2($messageId)
    {
        $answer = Answer::query()->where('message_id', $messageId)->where('message_id', $messageId)->get();
        Message::query()->where('id', $messageId)->update([
            'chat_in_progress' => '3',
            'text' => 'قیمت موجود نمیباشد'
            ]);
    }

    public function render()
    {
        $messages = Message::query()
            ->where('chat_in_progress', '2')
            ->orderBy('created_at', 'desc')
            ->get();
        $wait_for_price = Message::query()
            ->where('chat_in_progress', '3')
            ->orderBy('updated_at', 'desc')
            ->get();
        $ended_chats = Message::query()->where('chat_in_progress', '0')
            ->orderBy('updated_at', 'desc')
            ->get();
        $answers = Answer::query()
            ->whereHas('message', function ($q) {
                $q->where('chat_in_progress', '1');
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        $user = Auth::user();


        return view('livewire.admin.home.index', compact('messages', 'ended_chats', 'answers', 'wait_for_price', 'user'));
    }
}
