<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

use App\Models\WeeksPlayed;
use App\Models\Pick;

class ScheduleService
{
    public function __construct(
        private SettingService $settingService
    )
    {
        //
    }

    public function getScheduleByWeek(
        int $week,
        int $userId = 0
    ): array {
        $results = DB::select('
            SELECT
                `s`.`id` AS `id`,
                `t1`.`id` AS `homeId`,
                `t1`.`full_name` AS `home`,
                `t1`.`short_name` AS `homeShort`,
                `t1`.`abbreviation` AS `homeAbbreviated`,
                `t2`.`id` AS `awayId`,
                `t2`.`full_name` AS `away`,
                `t2`.`short_name` AS `awayShort`,
                `t2`.`abbreviation` AS `awayAbbreviated`,
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
                'homeShort' => $result->homeShort,
                'homeAbbreviated' => $result->homeAbbreviated,
                'awayId' => $result->awayId,
                'away' => $result->away,
                'awayShort' => $result->awayShort,
                'awayAbbreviated' => $result->awayAbbreviated,
                'type' => $result->type,
                'time' => $result->time,
                'picks' => $picks,
                'player' => $this->getWeekPicksByUser(
                    $userId,
                    $result->id,
                    $result->homeId
                )
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

    public function addWeekPlayed(
        int $week
    ): void {
        WeeksPlayed::updateOrCreate(
            [
                'week' => $week
            ],
            [
                'week' => $week
            ]
        );
    }

    public function checkSeasonInAction(): bool {
        return (bool) $this->settingService->getSettingByName('season_in_action');
    }

    public function getLastWeekPlayed(): int
    {
        $week = WeeksPlayed::select('week')->orderBy('week', 'desc')->first();
        return (int) isset($week['week']) ? $week['week'] : 0;
    }

    public function checkWeekPlayed(
        int $week
    ): bool {
        $week = WeeksPlayed::where('week', $week)->first();
        return $week ? true : false;
    }

    private function getWeekPicksByUser(
        int $userId,
        int $scheduleId,
        int $homeId
    ): array {
        if ($userId === 0) {
            return [
                'teamId' => $homeId,
                'pick' => 0
            ];
        }
        $picks = Pick::where([
            'user_id' => $userId,
            'schedule_id' => $scheduleId
        ])
        ->first();
        if (!$picks) {
            return [
                'teamId' => $homeId,
                'pick' => 0
            ];
            }
        return [
            'teamId' => $picks->team_id,
            'pick' => $picks->points
        ];
    }

    public function checkValidWeek(
        int $week,
        array $data
    ): bool {
        $data['week'] = $week;
        return $this->checkValidWeekForInitialResults(
            $data
        );
    }

    public function checkValidWeekForUpdateResults(
        array $data
    ): bool {
        if ($data['week'] < 1 || $data['week'] > $data['weeksPerSeason']) {
            return false;
        }
        $weekPlayed = $this->checkWeekPlayed(
            $data['week']
        );
        if (!$weekPlayed) {
            return false;
        }
        return true;
 
    }

    public function checkValidWeekForInitialResults(
        array $data
    ): bool {
        if ($data['week'] < 1 || $data['week'] > $data['weeksPerSeason']) {
            return false;
        }
        if (!$data['seasonInAction']) {
            return false;
        }
        $weekPlayed = $this->checkWeekPlayed(
            $data['week']
        );
        if ($weekPlayed) {
            return false;
        }
        return true;
    }

    public function checkValidWeekForRecalculatingResults(
        array $data
    ): bool {
        if ($data['week'] < 1 || $data['week'] > $data['weeksPerSeason']) {
            return false;
        }
        if (!$data['seasonInAction']) {
            return false;
        }
        $weekPlayed = $this->checkWeekPlayed(
            $data['week']
        );
        if (!$weekPlayed) {
            return false;
        }
        if ($data['week'] !== $this->getCurrentWeek() - 1) {
            return false;
        }
        return true;
    }

    public function validateGames(
        array $data,
        array $submittedGamesIds
    ): bool {
        // get the schedule IDs for this week
        $scheduleIds = $this->getWeekScheduleIds(
            $data['week']
        );
        if (count($scheduleIds) !== count($submittedGamesIds)) {
            return false;
        }
        
        if (array_diff($scheduleIds, $submittedGamesIds) || array_diff($submittedGamesIds, $scheduleIds)) {
            return false;
        }
        return true;
    }

    private function getWeekScheduleIds(
        int $week
    ): array {
        $schedule = $this->getScheduleByWeek(
            $week
        );
        $ids = [];
        foreach ($schedule as $game) {
            $ids[] = $game['id'];
        }
        return $ids;
    }

    public function getPicksByScheduleIdsForUsers(
        array $scheduleIds,
        Collection $users
    ): array {
        $scheduleIds = array_keys($scheduleIds);
        $updatedUsers = [];
        foreach ($users as &$user) {
            $updatedUsers[$user->id]['picks'] = Pick::select([
                'user_id',
                'schedule_id',
                'team_id',
                'points'
            ])
            ->whereIn('schedule_id', $scheduleIds)
            ->where('user_id', $user->id)
            ->get();
            $updatedUsers[$user->id]['name'] = $user->name;
            $updatedUsers[$user->id]['email'] = $user->email;
        }
        return $updatedUsers;
    }
}
