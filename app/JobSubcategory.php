<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobSubcategory extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'job_id', 'subcategory_id', 'value'
    ];
}
