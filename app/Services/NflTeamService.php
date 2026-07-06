<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\NflTeam;

class NflTeamService
{
    public function __construct()
    {
        ///
    }

    public function getAllNflTeams(): array
    {
        return NflTeam::all()->toArray();
    }
}
