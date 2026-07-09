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

    private function TotalWinsAction(array $siteData): array
    {
        $data = [];



        return $data;
    }

    private function HighestSeasonScoresAction(array $siteData): array
    {
        return Result::select(
                'users.name as name',
                'results.year',
                DB::raw('SUM(score) as total'),
                DB::raw('3 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->groupBy('results.year', 'results.user_id', 'users.name')
            ->orderByDesc('total')
            ->limit(self::MAX_NUMBER_OF_RECORDS)
            ->get()
            ->toArray();
    }

    private function LowestSeasonScoresAction(array $siteData): array
    {
        return Result::select(
                'users.name as name',
                'results.year',
                DB::raw('SUM(score) as total'),
                DB::raw('3 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->groupBy('results.year', 'results.user_id', 'users.name')
            ->orderBy('total', 'ASC')
            ->limit(self::MAX_NUMBER_OF_RECORDS)
            ->get()
            ->toArray();
    }

    private function HighestWeeklyScoresAction(array $siteData): array
    {
        $data = [];



        return $data;
    }

    private function LowestWeeklyScoresAction(array $siteData): array
    {
        $data = [];



        return $data;
    }
    
    private function TotalCenturyGamesAction(array $siteData): array
    {
        return Result::select(
            'users.name as name',
            DB::raw('COUNT(*) as total'),
            DB::raw('2 as columns')
        )
            ->join('users', 'users.id', '=', 'results.user_id')
            ->where('score', '>=', 100)
            ->groupBy('results.user_id', 'users.name')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }
    
    private function AveragePointsPerSeasonAction(array $siteData): array
    {
        $data = $this->TotalSeasonPointsAction($siteData);
        $users = [];
        $totalSeasons = $siteData['currentSeason'] - $siteData['firstSeason'];
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
    
    private function WeeksWonPerSeasonAction(array $siteData): array
    {
        $data = [];



        return $data;
    }
    
    private function AverageWeeksWonPerSeasonAction(array $siteData): array
    {
        $data = [];



        return $data;
    }
    
    private function TotalSeasonPointsAction(array $siteData): array
    {
        return Result::select(
                'users.name as name',
                DB::raw('SUM(score) as total'),
                DB::raw('2 as columns')
            )
            ->join('users', 'users.id', '=', 'results.user_id')
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
