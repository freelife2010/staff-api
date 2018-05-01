<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'group_id', 'sender_id', 'message', 'created_at', 'status', 'unread'
    ];
}
