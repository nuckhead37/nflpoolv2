<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Pick;
use Illuminate\Database\Eloquent\Collection;

class PickService
{
    public function __construct(
        private UserService $userService,
        private ScheduleService $scheduleService,
        private AdminService $adminService,
        private ResultService $resultService
    )
    {
        //
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

    public function getPicksAndScheduleByWeek(
        int $week
    ): array {
        $schedules = $this->scheduleService->getScheduleByWeek(
            $week
        );

        // have the schedule so get the users, their picks and a result
        $users = $this->userService->getAllUsers();

        $scheduleIds = $this->getScheduleIds(
            $schedules
        );

        $results = $this->resultService->getGameResultsByScheduleIds(
            $scheduleIds
        );

        foreach ($schedules as &$schedule) {
            foreach ($users as $user) {
                $schedule['users'][] = $this->getPicksByUser(
                    $schedule,
                    $user->id,
                    $results
                );
            }
        }

        return $schedules;
    }

    private function getScheduleIds(
        array $schedules
    ): array {
        $ids = [];
        foreach ($schedules as $schedule) {
            $ids[] = $schedule['id'];
        }
        return $ids;
    }

    private function getPicksByUser(
        array $schedule,
        int $userId,
        array $results
    ): array {
        $pickData = [];
        $pick = Pick::select(
                [
                    'user_id',
                    'team_id',
                    'points'
                ]
            )
            ->where('schedule_id', $schedule['id'])
            ->where('user_id', $userId)
            ->first();
            
        return [
                'user_id' => $userId,
                'team_id' => isset($pick->team_id) ? $pick->team_id : '--',
                'team' => $this->getTeamInfo(
                    $pick,
                    $schedule
                ),
                'points' => isset($pick->points) ? $pick->points : '--',
                'result' => $this->getWinnerFlag(
                    $schedule['id'],
                    $pick,
                    $results
                )
            ];
    }

    private function getTeamInfo(
        ?Pick $pick,
        array $schedule
    ): string {
        if (!$pick) {
            return '--';
        }
        return $schedule['homeId'] === $pick['team_id'] ? $schedule['home'] : $schedule['away'];
    }

    private function getWinnerFlag(
        int $scheduleId,
        ?Pick $pick,
        array $results
    ): string {
        if (!$pick) {
            return '--';
        }
        foreach ($results as $result) {
            if ($scheduleId === $result['schedule_id']) {
                return $pick['team_id'] === $result['nfl_team_id'] ? 'yes' : 'no';
            }
        }
        return '--';
    }
}
