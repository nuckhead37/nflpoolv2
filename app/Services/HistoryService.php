<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use \App\Models\Result;

class HistoryService
{
    public function __construct()
    {

    }

    public function validateYear(
        string|int $year = null,
        array $data
    ) : bool {
        if (!$year) {
            return false;
        }
        $year = (int) $year;
        if ($year >= $data['first_season'] && $year < $data['current_season']) {
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

        // CACHE players/users
        foreach ($yearData as $yd) {
            $data[] = [
                'week' => $yd['week'],
                'players' => $this->getPlayers(
                    $yd
                ),
                'winner' => $this->getWinner(
                    $yd
                )
            ];
        }
        return $data;
    }

    private function getWinner(
        array $data
    ): string {
        
    }
}
