<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $table = 'contact';

    protected $fillable = [
        'host', 'friends'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }
}
