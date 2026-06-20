<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Pick;

class PickService
{
    public function __construct(
        private UserService $userService,
        private ScheduleService $scheduleService
    )
    {

    }

    public function getPickWeeksAvailable(
        array $data
    ): array {
        $currentWeek = $this->scheduleService->getCurrentWeek();
        $picks = [];
        for ($x = $currentWeek; $x <= $data['weeksPerSeason']; $x++) {
            $picks[] = $x;
        }
        return $picks;
    }

}
