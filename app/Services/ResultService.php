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

    public function getSeasonResults(
        int $currentSeason
    ): array {

        /*
            need to check results for currentSeason

            return:

            ['weekX' =>
                [
                    'week' => X,
                    'users' => [
                        [
                            'name' => 'Clive',
                            'points' => 30
                        ],
                        [
                            'name' => 'Jim',
                            'points' => 3
                        ]
                    ],
                    'totals' => [
                        [
                            'name' => 'Clive',
                            'total' => 30
                        ],
                        [
                            'name' => 'Jim',
                            'total' => 3
                        ]                    
                    ]
                ]
            ]

        */

        return [
            ['week1' =>
                [
                    'week' => 1,
                    'users' => [
                        [
                            'name' => 'Clive',
                            'points' => 30
                        ],
                        [
                            'name' => 'Jim',
                            'points' => 3
                        ]
                    ],
                    'totals' => [
                        [
                            'name' => 'Jim',
                            'total' => 300,
                            'wins' => 2,
                            'tied' => 1
                        ],
                        [
                            'name' => 'Clive',
                            'total' => 299,
                            'wins' => 1,
                            'tied' => 2
                        ]   
                    ]
                ]
            ],
            ['week2' =>
                [
                    'week' => 2,
                    'users' => [
                        [
                            'name' => 'Jim',
                            'points' => 43
                        ],
                        [
                            'name' => 'Clive',
                            'points' => 35
                        ]
                    ],
                    'totals' => [
                        [
                            'name' => 'Jim',
                            'total' => 300,
                            'wins' => 2,
                            'tied' => 1
                        ],
                        [
                            'name' => 'Clive',
                            'total' => 299,
                            'wins' => 1,
                            'tied' => 2
                        ]   
                    ]
                ]
            ],
            ['week3' =>
                [
                    'week' => 3,
                    'users' => [
                        [
                            'name' => 'Jim',
                            'points' => 43
                        ],
                        [
                            'name' => 'Clive',
                            'points' => 35
                        ]
                    ],
                    'totals' => [
                        [
                            'name' => 'Jim',
                            'total' => 300,
                            'wins' => 2,
                            'tied' => 1
                        ],
                        [
                            'name' => 'Clive',
                            'total' => 299,
                            'wins' => 1,
                            'tied' => 2
                        ]   
                    ]
                ]
            ] 
        

        ];


    }

    public function getWeekResults(
        int $week
    ): array {

    }
}
