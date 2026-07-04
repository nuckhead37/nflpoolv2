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
                'tied' => $info->tied > 0 ? $info->tied : 0,
                'class' => ''
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
                'tied'  => '--',
                'class' => ''
            ];
        }
        return $data;
    }

    private function populateBlankUsersAndTotals(
        int $currentWeek
    ): array {
        $result = [];
        $users = $this->userService->getAllUsers();
        $totalUsers = count($users);
        foreach ($users as $user) {
            $users[] = [
                'name'   => $user->name,
                'points' => 0
            ];
        }
        $totals = $this->populateBlankTotals(
            $totalUsers
        );
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

    public function getSeasonResults(
        array $data
    ): array {
        $currentSeason = $data['currentSeason'];
        $results = $this->getResultsWithUsers(
            $currentSeason
        );
        $result = [];
        if (empty($results) && !$data['seasonInAction']) {
            return $result;
        }
        $currentWeek = $this->scheduleService->getCurrentWeek();

        // If no results and season is in action, populate blank users and totals
        if (empty($results) && $data['seasonInAction']) {
            return $this->populateBlankUsersAndTotals(
                $currentWeek
            );
        }

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
                        'tied'  => 0,
                        'class' => ''
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

        $result = $this->setLeaderClass(
            $result
        );

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

    private function setLeaderClass(
        array $data
    ): array {
        foreach ($data as &$weekData) {
            // Get the inner week array (week1, week2, etc.)
            $week = reset($weekData);
        
            // Find the highest total
            $maxTotal = max(array_column($week['totals'], 'total'));
        
            // Set the class
            foreach ($week['totals'] as &$player) {
                $player['class'] = ($player['total'] == $maxTotal) ? 'total-leader' : '';
            }
            unset($player);
        
            // Put the modified week back
            $weekData[key($weekData)] = $week;
        }
        unset($weekData);
        return $data;
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

    public function performValidation(
        array $data,    
        string $access,
        string $action
    ): bool {
        $check = $this->adminService->checkUserAccess(
            $access
        );
        if (!$check) {
            return false;
        }

        switch ($action) {
            case 'initial_results':
                $validWeek = $this->scheduleService->checkValidWeekForInitialResults(
                    $data
                );
                break;
            case 'update_results':
                $validWeek = $this->scheduleService->checkValidWeekForUpdateResults(
                    $data
                );
                break;
            default:
                return false;
        }

        if (!$validWeek) {
            return false;
        }
        return true;
    }

    public function calculateUserTotalForWeek(
        array $games,
        array $users,
        int $week,
        int $year
    ): array {
        $players = [];
        foreach ($users as $user) {
            foreach ($user['picks'] as $us) {
                $userIdHash = hash('md4', $us->user_id);
                if (!array_key_exists($userIdHash, $players)) {
                    $players[$userIdHash] = [
                        'user_id' => $us->user_id,
                        'total' => 0,
                        'winner' => 0,
                        'tied' => 0,
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'week' => $week,
                        'year' => $year
                    ];
                }
                foreach ($games as $scheduleId => $winnerId) {
                    if ((int) $scheduleId === (int) $us->schedule_id && (int) $winnerId === (int) $us->team_id) {
                        $players[$userIdHash]['total'] += $us->points;
                    }
                }
            }
        }
        return $players;
    }

    private function writeResults(
        array $players
    ): void {
        foreach ($players as $player) {
            Result::updateOrCreate(
                [
                    'week' => $player['week'],
                    'year' => $player['year'],
                    'user_id' => $player['user_id']
                ],
                [
                    'year' => $player['year'],
                    'week' => $player['week'],
                    'user_id' => $player['user_id'],
                    'score' => $player['total'],
                    'winner' => $player['winner'],
                    'tied' => $player['tied']
                ]
            );
        }
    }

    public function calculateWinner(
        array $results
    ): array {
        usort($results, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        if ($results[0]['total'] > $results[1]['total']) {
            $results[0]['winner'] = 1;
        } elseif ($results[0]['total'] === $results[1]['total']) {
            $results[0]['tied'] = 1;
            $results[1]['tied'] = 1;
        }

        $this->writeResults(
            $results
        );

        return $results;
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
