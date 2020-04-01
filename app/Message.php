<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'content', 'is_read', 'from_id', 'to_id'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
