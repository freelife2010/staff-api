<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'company_name', 'website_url', 'phone_visible', 'purchase_level', 'purchase_time'
    ];
}
