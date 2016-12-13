<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'openid', 'voteid', 'selected',
    ];
    protected $primaryKey = 'id';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
