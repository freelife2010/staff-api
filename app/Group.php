<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'creator_id', 'members'
    ];
}
