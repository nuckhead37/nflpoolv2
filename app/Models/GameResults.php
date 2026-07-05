<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameResults extends Model
{
    protected $fillable = [
        'id',
        'schedule_id',
        'nfl_team_id'
    ];
}
