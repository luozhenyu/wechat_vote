<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'voteid', 'title', 'description', 'count', 'minSelect', 'maxSelect', 'startTime', 'endTime', 'options'
    ];

    protected $primaryKey = 'voteid';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
