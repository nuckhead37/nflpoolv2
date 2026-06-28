<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Pick;
use App\Models\Result;
use App\Models\GameResults;

class ResultService
{
    public function __construct(
        private UserService $userService,
        private ScheduleService $scheduleService,
        private HistoryService $historyService,
        private AdminService $adminService
    )
    {
        //
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
    
    private function getResultsWithUsers(
        int $year
    ): array {
        return DB::table('results')
        // Result::where('year', $year)
            ->join('users','users.id', '=', 'results.user_id')
            ->orderBy('week', 'ASC')
            ->orderBy('score', 'DESC')
            ->select(
                'results.user_id',
                'results.week',
                'results.score',
                'results.winner',
                'results.tied',
                'users.name'
            )
            ->where('results.year', $year)
            ->get()
            ->toArray();
    }

    private function populateBlankUsers(
        int $numberUsers
    ): array {
        $data = [];
        for ($x=0; $x<$numberUsers; $x++) {
            $data[] = ['name' => '--', 'points' => '--'];
        }
        return $data;
    }

    private function populateBlankTotals(
        int $numberUsers
    ): array {
        $data = [];
        for ($x=0; $x<$numberUsers; $x++) {
            $data[] = [
                'name'  => '--',
                'total' => '--',
                'wins'  => '--',
                'tied'  => '--'
            ];
        }
        return $data;
    }

    public function getSeasonResults(
        int $currentSeason
    ): array {
        $results = $this->getResultsWithUsers(
            $currentSeason
        );
        if (empty($results)) {
            return [];
        }
        $currentWeek = $this->scheduleService->getCurrentWeek();

        // Group by week
        $weeks = [];
        foreach ($results as $row) {
            $weeks[$row->week][] = $row;
        }
        
        ksort($weeks);
        
        $result = [];
        $runningTotals = [];
        
        foreach ($weeks as $weekNumber => $rows) {
        
            $users = [];
        
            foreach ($rows as $row) {
        
                $users[] = [
                    'name'   => $row->name,
                    'points' => $row->score
                ];
        
                // Initialise user if first encounter
                if (!isset($runningTotals[$row->user_id])) {
                    $runningTotals[$row->user_id] = [
                        'name'  => $row->name,
                        'total' => 0,
                        'wins'  => 0,
                        'tied'  => 0
                    ];
                }
        
                // Update cumulative totals
                $runningTotals[$row->user_id]['total'] += $row->score;
                $runningTotals[$row->user_id]['wins']  += $row->winner;
                $runningTotals[$row->user_id]['tied']  += $row->tied;
            }
        
            // Convert running totals to indexed array
            $totals = array_values($runningTotals);
        
            // Optional: sort totals by highest score
            usort($totals, function ($a, $b) {
                return $b['total'] <=> $a['total'];
            });
        
            $result[] = [
                'week' . $weekNumber => [
                    'week'   => $weekNumber,
                    'users'  => $users,
                    'totals' => $totals
                ]
            ];
        }

        $totalUsers = count($users);

        $result[] = [
            'week' . $currentWeek => [
                'week'   => $currentWeek,
                'users'  => $this->populateBlankUsers(
                    $totalUsers
                ),
                'totals' => $this->PopulateBlankTotals(
                    $totalUsers
                )
            ]
        ];

        return $result;
    }

    public function getGameResultsByScheduleIds(
        array $scheduleIds
    ): array {
        $results = GameResults::select(
            [
                'schedule_id',
                'nfl_team_id'
            ]
        )
        ->whereIn('schedule_id', $scheduleIds)
        ->get();
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'nfl_team_id' => $result->nfl_team_id,
                'schedule_id' => $result->schedule_id
            ];
        }
        return $data;
    }

    public function performBasicValidation(
        array $data
    ): bool {
        $check = $this->adminService->checkUserAccess(
            'enter results'
        );
        if (!$check) {
            return false;
        }

        $validWeek = $this->scheduleService->checkValidWeekForInitialResults(
            $data
        );

        if (!$validWeek) {
            return false;
        }
        return true;
    }

    public function enterGameResults(
        array $games
    ): void {
        $ids = collect($games)
            ->map(function ($points, $game) {
                return GameResults::updateOrCreate(
                    [
                        'schedule_id' => $game
                    ],
                    [
                        'schedule_id' => $game,
                        'nfl_team_id' => $points
                    ]
                    )->id;
            })
            ->all();
    }
}
