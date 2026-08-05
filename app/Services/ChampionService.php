<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Champion;

class ChampionService
{
    public function __construct(
        private UserService $userService
    )
    {

    }

    public function getCurrentChampion(): string {
        $id = Champion::select('user_id')->orderBy('year', 'desc')->first();
        return $this->userService->getNameById($id->user_id);
    }

    public function createChampionRecord(
        int $year,
        array $champion
    ): void {
        $championRecord = Champion::where('user_id', $champion['champion_id'])
            ->where('year', $year)
            ->first();

        if (!$championRecord) {
            $championRecord = new Champion();
            $championRecord->user_id = $champion['champion_id'];
            $championRecord->year = $year;
            $championRecord->save();
        }
    }

    public function getChampion(
        array $totals
    ): array {
        foreach ($totals as $total) {
            if ($total->user_id === $total->champion_id) {
                return [
                    'image' => $total->winner_image,
                    'champion' => $total->name,
                    'champion_id' => $total->champion_id
                ];
            }
        }
        return [
            'image' => 'images/weekly_winner.png',
            'champion' => $totals[0]->name,
            'champion_id' => $totals[0]->champion_id
        ];
    }

    public function getAllChampions(): array
    {
        return Champion::select([
            'users.name as name',
            'champions.year as year'
        ])
            ->join('users','users.id', '=', 'champions.user_id')
            ->orderBy('champions.year', 'DESC')
            ->get()
            ->toArray();
    }
}
