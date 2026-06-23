<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminService
{
    public function __construct(
        private UserService $userService,
        private ScheduleService $scheduleService
    )
    {

    }

    public function checkUserAccess(
        string $permission
    ): bool {
        try {
            $user = $this->userService->getUser();
            if (!$user) {
                return false;
            }

            if (!$user->hasPermissionTo($permission)) {
                return false;
            }
        } catch(Exception) {
            return false;
        }
        return true;
    }

    public function canInputResults(): string
    {
        return '';
    }

    public function canUpdatePicks(): string
    {
        return $this->canInputResults();
    }

    public function canCreateNewSeason(
        array $data
    ): string {
        if ($data['seasonInAction']) {
            return ' disabled';
        }
        $lastWeekPlayed = $this->scheduleService->getLastWeekPlayed();
        if ($data['weeksPerSeason'] === $lastWeekPlayed) {
            return '';
        }
        return 'disabled';
    }
}
