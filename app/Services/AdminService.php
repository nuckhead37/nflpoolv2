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

    public function canRecalculateResult(
        array $data
    ): array {
        if (!$this->checkUserAccess('update results')) {
            return [' disabled', 0];
        }
        $week = 0;
        if ($data['seasonInAction']) {
            $week = $this->scheduleService->getCurrentWeek();
            $week -= $week > 1 ? 1 : 0;
        } else {
            $week = $this->scheduleService->getCurrentWeek();
            if ($week === 0) {
                return [' disabled', $week];
            }
            $week = $data['weeksPerSeason'];
        }
        return ['', $week];
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
        if ($this->checkUserAccess('create season')) {
            return ' disabled';
        }
        $lastWeekPlayed = $this->scheduleService->getLastWeekPlayed();
        if ($data['weeksPerSeason'] === $lastWeekPlayed) {
            return '';
        }
        return 'disabled';
    }
}
