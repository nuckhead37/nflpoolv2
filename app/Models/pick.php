<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    //
    protected $fillable = [
        'team_id',
        'schedule_id',
        'points',
        'user_id'
    ];
}
