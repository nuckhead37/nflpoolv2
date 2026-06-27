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

    public function showMakeEdit(
        array $data
    ): bool {
        if (!$data['user']) {
            return false;
        }
        return !$this->scheduleService->checkWeekPlayed(
            $data['week']
        );
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

        $weekPlayed = $this->scheduleService->checkWeekPlayed(
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
                    $results,
                    $weekPlayed
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
        array $results,
        bool $weekPlayed
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
                'teamShort' => $this->getTeamInfo(
                    $pick,
                    $schedule,
                    true
                ),
                'points' => isset($pick->points) ? $pick->points : '--',
                'result' => $this->getWinnerFlag(
                    $schedule['id'],
                    $pick,
                    $results,
                    $weekPlayed
                )
            ];
    }

    private function getTeamInfo(
        ?Pick $pick,
        array $schedule,
        bool $useShort = false
    ): string {
        if (!$pick) {
            return '--';
        }
        if ($useShort) {
            return $schedule['homeId'] === $pick['team_id'] ? $schedule['homeShort'] : $schedule['awayShort'];
        }
        return $schedule['homeId'] === $pick['team_id'] ? $schedule['home'] : $schedule['away'];
    }

    private function getWinnerFlag(
        int $scheduleId,
        ?Pick $pick,
        array $results,
        bool $weekPlayed
    ): string {
        if (!$weekPlayed) {
            return 'none';
        }
        if (empty($results)) {
            return 'none';
        }
        if (!$pick) {
            return 'cross';
        }
        foreach ($results as $result) {
            if ($scheduleId === $result['schedule_id']) {
                return $pick['team_id'] === $result['nfl_team_id'] ? 'tick' : 'cross';
            }
        }
        return 'cross';
    }

    public function enterResults(): View
    {
        $data = $this->helperService->getBasicInfo();


        // what week?


        return view('admin/enter-results', $data);
    }

    // private function buildPlayerPicks(
    //     array $games,
    //     Collection $picks
    // ): array {
    //     foreach ($games as $game)  {
    //         $

    //         foreach ($picks as $picks)  {
    //             if ($pick->schedule_id === $game['id']) {
                
    //         }

    //     }
    //     foreach ($picks as $pick) {
    //         foreach ($games as &$game) {
    //             if ($pick->schedule_id === $game['id']) {
    //                 $game['player'] = [
    //                     'team_id' => $pick->team_id,
    //                     'points' => $pick->points
    //                 ];
    //             }
    //         }
    //     }
    //     return $games;
    // }

    public function getPickValue(
        int $scheduleId,
        array $picks
    ): int {
        foreach ($picks as $pick) {
            if ((int) $pick->game === $scheduleId) {
                return $pick->pick;
            }
        }
        return 0;
    }

    public function savePickData(
        array $pickData
    ): bool {
        try {
            foreach ($pickData as $pData) {
                Pick::updateOrCreate(
                    [
                        'user_id' => $pData['user_id'],
                        'schedule_id' => $pData['schedule_id'],
                    ],
                    [
                        'user_id' => $pData['user_id'],
                        'schedule_id' => $pData['schedule_id'],
                        'team_id' => $pData['team_id'],
                        'points' => $pData['points']
                    ]
                );
            }
        } catch (Exception $e) {

        }



        return true;
    }
}
