<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'first_name', 'last_name', 'gender', 'birth_date', 'categories', 'about', 'experience', 'available_days', 'languages'
    ];
}
