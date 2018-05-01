<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'latitude', 'longitude', 'address'
    ];
}
