<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'job_id', 'user_id', 'status', 'like', 'applied_at'
    ];
}
