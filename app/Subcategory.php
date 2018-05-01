<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'category_id', 'name', 'type'
    ];
}
