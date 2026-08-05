<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse as Redirect;
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
        private AdminService $adminService,
        private EmailService $emailService
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

    public function getSeasonResultsByYear(
        int $year
    ): array {
        $results = $this->getResultsWithUsers(
            $year
        );

        $result = [];
        if (empty($results)) {
            return $result;
        }

        [$result, $users] = $this->processResults(
            $results
        );
        return $result;
    }

    private function checkForWinValues(
        array $weeks
    ): bool {
        $total = 0;
        foreach ($weeks as $week) {
            foreach ($week as $wk) {
                $total = $total + $wk->winner + $wk->tied;
            }
        }
        return (bool) $total;
    }

    private function processResults(
        array $results
    ): array {
        // Group by week
        $weeks = [];
        foreach ($results as $row) {
            $weeks[$row->week][] = $row;
        }
        
        ksort($weeks);
        
        $result = [];
        $runningTotals = [];
        

        $showTotals = $this->checkForWinValues(
            $weeks
        );

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
                if (!$showTotals) {
                    $runningTotals[$row->user_id]['wins'] = '--';
                    $runningTotals[$row->user_id]['tied'] = '--';
                } else {
                    $runningTotals[$row->user_id]['wins'] += $row->winner;
                    $runningTotals[$row->user_id]['tied'] += $row->tied;
                }
                $runningTotals[$row->user_id]['total'] += $row->score;
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

        return [$result, $users];
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

        [$result, $users] = $this->processResults(
            $results
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

    public function getGameWinners(
        array $schedules
    ): array {
        $scheduleIds = [];
        foreach ($schedules as $schedule) {
            $scheduleIds[] = $schedule['id'];
        }

        // $schedules now contains all the games. Need to extract the winners and update.

        $results = $this->getResultsByScheduleIds(
            $scheduleIds
        );

        foreach ($schedules as &$schedule) {
            foreach ($results as $result) {
                if ($schedule['id'] === $result['schedule_id']) {
                    $schedule['player']['teamId'] = $result['nfl_team_id'];
                }
            }
        }

        return $schedules;
    }

    public function getResultsByScheduleIds(
        array $scheduleIds
    ): array {
        return $this->getGameResultsByScheduleIds(
            $scheduleIds
        );
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
            case Result::INITIAL_RESULTS:
                $validWeek = $this->scheduleService->checkValidWeekForInitialResults(
                    $data
                );
                break;
            case Result::UPDATE_RESULTS:
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
            foreach ($user['picks'] as $pick) {
                $userHash = hash('md4', (string) $pick->user_id);
    
                if (!isset($players[$userHash])) {
                    $players[$userHash] = [
                        'user_id' => $pick->user_id,
                        'total'   => 0,
                        'winner'  => 0,
                        'tied'    => 0,
                        'name'    => $user['name'],
                        'email'   => $user['email'],
                        'week'    => $week,
                        'year'    => $year,
                    ];
                }
    
                $winnerId = $games[$pick->schedule_id] ?? null;
    
                if ($winnerId !== null && (int) $winnerId === (int) $pick->team_id) {
                    $players[$userHash]['total'] += $pick->points;
                }
            }
        }

        return $players;
    }

    public function calculateUserTotalForWeek_OLD(
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

    public function calculateSeasonTotals(
        int $year
    ): array {
        $totals = Result::query()
            ->select(
                'results.user_id',
                'users.name',
                'users.winner_image'
            )
            ->selectRaw('SUM(results.score) as total')
            ->selectRaw('SUM(results.winner) as wins')
            ->selectRaw('SUM(results.tied) as tied')
            ->join('users', 'users.id', '=', 'results.user_id')
            ->where('results.year', $year)
            ->groupBy('results.user_id', 'users.name');
        
        return DB::query()
            ->fromSub($totals, 't')
            ->crossJoinSub(
                DB::query()
                    ->fromSub($totals, 'c')
                    ->select(
                        'name as champion',
                        'user_id as champion_id',
                        'winner_image'
                    )
                    ->orderByDesc('total')
                    ->limit(1),
                'champ'
            )
            ->select('t.*', 'champ.champion', 'champ.champion_id', 'champ.winner_image')
            ->orderByDesc('t.total')
            ->get()
            ->toArray();
    }

    private function getReturnUrl(
        string $resultType
    ): string {
        switch ($resultType) {
            case Result::UPDATE_RESULTS:
               $url = '/recalculate-results';
                break;
            default:
            case Result::INITIAL_RESULTS:
                $url = '/enter-results';
                break;
        }
        return $url;
    }

    public function processGamesData(
        Request $request,
        array $data,
        $permission,
        $resultType
    ): Redirect {
        $check = $this->performValidation(
            $data,
            $permission,
            $resultType
        );

        $returnUrl = $this->getReturnUrl(
            $resultType
        );

        if (!$check) {
            return redirect($returnUrl)
                ->with('error', true);
        }

        if (!$request->has('games')) {
            return redirect($returnUrl)
            ->with('error', true);
        }

        $games = $request->games;

        $scheduleIds = array_keys($games);

        // check that the games all match the presented data.
        $validateGames = $this->scheduleService->validateGames(
            $data,
            $scheduleIds
        );

        if (!$validateGames) {
            return redirect($returnUrl)
                ->with('error', true);
        }

        // write the games
        $this->enterGameResults(
            $games
        );

        $users = $this->userService->getAllUsers();

        $users = $this->scheduleService->getPicksByScheduleIdsForUsers(
            $games,
            $users
        );

        $results = $this->calculateUserTotalForWeek(
            $games,
            $users,
            $data['week'],
            $data['currentSeason']
        );

        $results = $this->calculateWinner(
            $results
        );

        $this->scheduleService->addWeekPlayed(
            $data['week']
        );

        $totals = $this->calculateSeasonTotals(
            $data['currentSeason']
        );

        if ($data['week'] === $data['weeksPerSeason']) {
            $champion = $this->championService->getChampion(
                $totals
            );

            $this->championService->createChampionRecord(
                $data['currentSeason'],
                $champion
            );

            $this->settingService->updateSettingByName(
                'season_in_action',
                false
            );
    
            $emailData = $this->emailService->generateSeasonWinnerEmail(
                $data,
                $results,
                $totals,
                $champion
            );
            $template = 'emails/season-winner';
        } else {
            // normal week
            $emailData = $this->emailService->generateWeeklyWinnerEmail(
                $data,
                $results,
                $totals
            );
            $template = 'emails/weekly-winner';
        }

        $this->emailService->sendEmails(
            $emailData,
            $users,
            $template
        );

        return redirect('/current');
    }
}
