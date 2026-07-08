<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public const UPDATE_RESULTS = 'update_results';
    public const INITIAL_RESULTS = 'initial_results';
    
    protected $fillable = [
        'year',
        'week',
        'user_id',
        'score',
        'winner',
        'tied'
    ];
}
