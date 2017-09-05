<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];
    protected $casts = [
        'content' => 'array'
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}