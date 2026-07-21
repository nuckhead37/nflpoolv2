<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use \App\Models\Result;

class HistoryService
{
    public function __construct(
        private ChampionService $championService
    )
    {
        //
    }

    public function validateYear(
        string|int $year = null,
        array $data
    ) : bool {
        if (!$year) {
            return false;
        }
        $year = (int) $year;
        $latest = $data['seasonInAction'] ? $data['currentSeason'] + 1 : $data['currentSeason'];
        if ($year >= $data['firstSeason'] && $year < $latest) {
            return true;
        }
        return false;
    }

    public function getHistoryByYear(
        int $year
    ): array {
        $data = $yearData = [];
        $yearData = Result::where('year', $year)
            ->orderBy('week', 'ASC')
            ->orderBy('score', 'DESC')
            ->get()
            ->toArray();

        $champions = $this->championService->getAllChampions();


        // CACHE players/users
        foreach ($yearData as $yd) {
            $data[] = [
                'week' => $yd['week'],
                'players' => $this->getPlayers(
                    $yd
                ),
                'winner' => $this->findChampionFromHistory(
                    $yd['year'],
                    $champions
                )
            ];
        }
        return $data;
    }

    private function getPlayers(
        array $data
    ): array {
        return [];
    }

    // private function getWinner(
    //     array $data
    // ): string {
        
    // }

    public function getAllHistoryYears(
        int $firstSeason,
        int $currentSeason,
        bool $seasonInAction
    ): array {
        $start = $seasonInAction ? $currentSeason - 1 : $currentSeason;
        $years = [];
        if ($seasonInAction) {
            $years[] = $this->addBlankYear(
                $currentSeason
            );
        }
        $champions = $this->championService->getAllChampions();
        for ($x = $start; $x >= $firstSeason; $x--) {
            $years[] = [
                'linkUrl' => '/history/' . $x,
                'year' => $x,
                'winner' => $this->findChampionFromHistory(
                    $x,
                    $champions
                )
            ];
        }
        return $years;
    }

    private function addBlankYear(
        int $year
    ): array {
        return [
            'linkUrl' => '/history/' . $year,
            'year' => $year,
            'winner' => '---'
        ];
    }

    private function findChampionFromHistory(
        int $year,
        array $champions
    ): string {
        foreach ($champions as $item) {
            if ((string) $item['year'] === (string) $year) {
                return 'Champion: ' . $item['name'];
            }
        }
        return '---';
    }
}
