<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

use App\Models\WeeksPlayed;

class ScheduleService
{
    public function __construct(
        private SettingService $settingService
    )
    {
        //
    }

    public function getScheduleByWeek(
        int $week
    ): array {
        $results = DB::select('
            SELECT
                `s`.`id` AS `id`,
                `t1`.`id` AS `homeId`,
                `t1`.`full_name` AS `home`,
                `t2`.`id` AS `awayId`,
                `t2`.`full_name` AS `away`,
                `tm`.`time` AS `time`,
                `ty`.`type` AS `type`
            FROM
                `schedules` `s`
            LEFT JOIN
                `nfl_teams` `t1` ON (`t1`.`id` = `s`.`home_id`)
            LEFT JOIN
                `nfl_teams` `t2` ON (`t2`.`id` = `s`.`away_id`)
            LEFT JOIN
                `game_types` `ty` ON (`ty`.`id` = `s`.`game_type_id`)
            LEFT JOIN
                `game_times` `tm` ON (`tm`.`id` = `s`.`game_time_id`)
            WHERE `s`.`week` = ? 
            ORDER BY `s`.`order` ASC',
            [$week]);
        $games = [];
        $count = count($results);
        $picks = $this->buildPicks(
            $count
        );
        foreach ($results as $result) {
            $games[] = [
                'id' => $result->id,
                'homeId' => $result->homeId,
                'home' => $result->home,
                'awayId' => $result->awayId,
                'away' => $result->away,
                'type' => $result->type,
                'time' => $result->time,
                'picks' => $picks
            ];
        }
        return $games;
    }

    private function buildPicks(
        int $count
    ): array {
        $picks = [];
        for ($x=0; $x<$count; $x++)  {
            $picks[] = ($x+1);
        }
        return $picks;
    }

    public function getCurrentWeek(): int
    {
        return $this->getLastWeekPlayed() + 1;
    }

    public function checkSeasonInAction(): bool {
        return (bool) $this->settingService->getSettingByName('season_in_action');
    }

    public function getLastWeekPlayed(): int
    {
        $week = WeeksPlayed::select('week')->orderBy('week', 'desc')->first();
        return (int) isset($week['week']) ? $week['week'] : 0;
    }
}
