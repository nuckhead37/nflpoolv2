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
        private ScheduleService $scheduleService,
        private AdminService $adminService,

    )
    {

    }

    public function getPickWeeksAvailable(
        int $weeksPerSeason
    ): array {
        $currentWeek = $this->scheduleService->getCurrentWeek();
        $picks = [];
        for ($x = $currentWeek; $x <= $weeksPerSeason; $x++) {
            $picks[] = $x;
        }
        return $picks;
    }

    public function checkPickOptions(
        bool $userLoggedIn,
        bool $seasonInAction,
        int $weeksPerSeason
    ): array {
        if ($userLoggedIn && $seasonInAction) {
            return $this->getPickWeeksAvailable(
                $weeksPerSeason
            );
        }
        return [];
    }

    public function checkPickAvailable(
        int $week = 0,
        int $weeksPerSeason
    ): bool {
        $check = $this->adminService->checkUserAccess(
            'make picks'
        );
        if (!$check) {
            return false;
        }
        // IS THE SEASON IN ACTION? SIMILAR FUNCTION TO USE ON HOMEPAGE?

        $currentWeek = $this->scheduleService->getCurrentWeek();
        if ($week === 0 || $week < $currentWeek || $week > $weeksPerSeason) {
            return false;
        }
        return true;
    }
}
