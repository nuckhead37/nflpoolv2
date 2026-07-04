<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'year',
        'week',
        'user_id',
        'score',
        'winner',
        'tied'
    ];
}
