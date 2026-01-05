<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message  extends Model
{
    protected $guarded = [];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
