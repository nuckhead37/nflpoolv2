<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Champion;

class ChampionService
{
    public function __construct(
        private UserService $userService
    )
    {

    }

    public function getCurrentChampion(): string {
        $id = Champion::select('user_id')->orderBy('year', 'desc')->first();
        return $this->userService->getNameById($id->user_id);
    }
}
