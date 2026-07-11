<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse as Redirect;
use Exception;
use App\Models\Stat;
use App\Models\Result;
use App\Models\User;
use App\Models\Champion;

class StatsService
{
    private const MAX_NUMBER_OF_RECORDS = 10;

    public function __construct(

    )
    {
        //
    }

    public function getStatsByName(
        array $names,
        array $siteData,
        int $userId = 0
    ): array {
        $data = [];
        foreach ($names as $name) {
            $key = array_key_first($name);
            $data[$key] = [
                'title' => $name[$key],
                'data' => $this->process(
                    $key,
                    $siteData
                )
            ];
        }
        return $data;
    }

    private function process(
        string $key,
        array $siteData
    ): array {
        $method = $key . 'Action';
    
        if (!method_exists($this, $method)) {
            return ['nothing'];
        }
    
        return $this->{$method}(
            $siteData
        );
    }

    private function TotalWeeksWonAction(
        array $siteData
    ): array {

        $userWins = Result::select(
                'users.name as name',
                DB::raw('SUM(winner) as total'),
                DB::raw('2 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->where('winner', 1)
            ->whereIn('results.user_id', $siteData['userIds'])
            ->groupBy('users.id', 'users.name');
    
        $tiedWins = Result::select(
                DB::raw("'Tied' as name"),
                DB::raw('FLOOR(COUNT(*) / 2) as total'),
                DB::raw('2 as columns')
            )
            ->whereIn('results.user_id', $siteData['userIds'])
            ->where('tied', 1);
        
        return $userWins
            ->unionAll($tiedWins)
            ->orderByDesc('total')
            ->limit(self::MAX_NUMBER_OF_RECORDS)
            ->get()
            ->toArray();
    }

    private function HighestSeasonScoresAction(
        array $siteData
    ): array {
        return Result::select(
                'users.name as name',
                'results.year',
                DB::raw('SUM(score) as total'),
                DB::raw('3 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->whereIn('results.user_id', $siteData['userIds'])
            ->groupBy('results.year', 'results.user_id', 'users.name')
            ->orderByDesc('total')
            ->limit(self::MAX_NUMBER_OF_RECORDS)
            ->get()
            ->toArray();
    }

    private function LowestSeasonScoresAction(
        array $siteData
    ): array {
        return Result::select(
                'users.name as name',
                'results.year',
                DB::raw('SUM(score) as total'),
                DB::raw('3 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->whereIn('results.user_id', $siteData['userIds'])
            ->groupBy('results.year', 'results.user_id', 'users.name')
            ->orderBy('total', 'ASC')
            ->limit(self::MAX_NUMBER_OF_RECORDS)
            ->get()
            ->toArray();
    }

    private function HighestWeeklyScoresAction(
        array $siteData
    ): array {
        return $this->getWeeklyScoresData(
            $siteData['userIds'],
            'DESC'
        );
    }

    private function getWeeklyScoresData(
        array $userIds,
        string $direction
    ): array {
        return Result::select(
            'users.name as name',
            'results.year',
            'results.score as total',
            'results.week as week',
            DB::raw('4 as columns')
        )
        ->join('users', 'users.id', '=', 'results.user_id')
        ->whereIn('results.user_id', $userIds)
        ->orderBy('total', $direction)
        ->limit(self::MAX_NUMBER_OF_RECORDS)
        ->get()
        ->toArray();
    }

    private function LowestWeeklyScoresAction(
        array $siteData
    ): array {
        return $this->getWeeklyScoresData(
            $siteData['userIds'],
            'ASC'
        );
    }
    
    private function TotalCenturyGamesAction(
        array $siteData
    ): array {
        return Result::select(
            'users.name as name',
            DB::raw('COUNT(*) as total'),
            DB::raw('2 as columns')
        )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->where('score', '>=', 100)
            ->whereIn('results.user_id', $siteData['userIds'])
            ->groupBy('results.user_id', 'users.name')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }
    
    private function AveragePointsPerSeasonAction(
        array $siteData
    ): array {
        $data = $this->TotalSeasonPointsAction($siteData);
        $users = [];
        $totalSeasons = $this->getTotalSeason(
            $siteData
        );
        foreach ($data as $key => $val) {
            $total = (float) str_replace(',', '', $val['total']);
            $users[] = [
                'name' => $val['name'],
                'total' => number_format($total / $totalSeasons, 1),
                'columns' => 2
            ];
        }


        return $users;
    }

    private function getTotalSeason(
        array $siteData
    ): int {
        return $siteData['currentSeason'] - $siteData['firstSeason'];
    }
    
    private function WeeksWonPerSeasonAction(
        array $siteData
    ): array {
        $data = [];



        return $data;
    }

    private function TotalSeasonsWonAction(
        array $siteData
    ): array {
        return Champion::select(
            'users.name as name',
            DB::raw('COUNT(champions.user_id) as total'),
            DB::raw('2 as columns')
        )
        ->join('users', 'users.id', '=', 'champions.user_id')
        ->whereIn('champions.user_id', $siteData['userIds'])
        ->groupBy('champions.user_id')
        ->orderByDesc('total')
        ->get()
        ->toArray();
    }
    
    private function AverageWeeksWonPerSeasonAction(
        array $siteData
    ): array {
        $results = $this->TotalWeeksWonAction(
            $siteData
        );
        $totalSeasons = $this->getTotalSeason(
            $siteData
        );
        foreach ($results as &$result) {
            $result['total'] = number_format(
                ($result['total'] / $totalSeasons),
                2
            );
        }
        return $results;
    }
    
    private function TotalSeasonPointsAction(
        array $siteData
    ): array {
        return Result::select(
                'users.name as name',
                DB::raw('SUM(score) as total'),
                DB::raw('2 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->whereIn('results.user_id', $siteData['userIds'])
            ->groupBy('results.user_id', 'users.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($result) {
                $result->total = number_format($result->total, 1);
                return $result;
            })
            ->toArray();
    }
}
