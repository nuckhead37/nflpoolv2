<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Pick;
use App\Models\Result;

class ResultService
{
    public function __construct(
        private UserService $userService,
        private ScheduleService $scheduleService
    )
    {

    }

    public function getCurrentSeasonTotals(
        array $data
    ): array {
        $lastWeekPlayed = $this->scheduleService->getLastWeekPlayed();
        if ($lastWeekPlayed < 1) {
            return [];
        }

        return $this->getTotalsByUsers(
            (int) $data['currentSeason']
        );
    }

    public function getTotalsByUsers(
        int $currentSeason
    ): array {

        // get the players, current totals and weeks won
        $users = $this->userService->getAllUsers();

        $results = [];
        foreach ($users as $user) {
            $info = $this->getInfoTotals(
                $user->id,
                $currentSeason
            );
            $results[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'total' => $info->total,
                'wins' => $info->wins,
                'tied' => $info->tied > 0 ? $info->tied : 0
            ];
        }
        return $this->sortByTotal(
            $results
        );
    }

    private function getInfoTotals(
        int $id,
        int $season
    ): Result {
        return Result::where('user_id', $id)
            ->where('year', $season)
            ->selectRaw('SUM(`score`) as `total`, SUM(`winner`) as `wins`, SUM(`tied`) AS `tied`')
            ->first();
    }

    private function sortByTotal(
        array $results
    ): array {
        usort($results, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        return $results;
    }
}
