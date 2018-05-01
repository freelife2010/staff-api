<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'category_id', 'title', 'description', 'profile_wanted', 'start_date', 'end_date',
        'start_time', 'end_time', 'payment_type', 'price', 'unit', 'latitude', 'longitude', 'address', 'status', 'created_at'
    ];
}
