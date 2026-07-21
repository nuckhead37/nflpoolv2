<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse as Redirect;
use Illuminate\View\View;

use App\Services\HelperService;
use App\Services\StatsService;
use App\Services\UserService;

use App\Models\Stat;

class StatsController extends Controller
{
    //
    public function __construct(
        private StatsService $statService,
        private HelperService $helperService,
        private UserService $userService
    )
    {
        //
    }

    public function stats(): View
    {
        $data = $this->helperService->getBasicInfo();
        $data['userIds'] = $this->userService->getAllUserIds();
        $data['userSelectOptions'] = $this->userService->getUsersForSelect();

        $data['column1'] = $this->statService->getStatsByName(
            [
                Stat::TOTAL_SEASONS_WON,
                Stat::TOTAL_WEEKS_WON,
                Stat::TOTAL_SEASON_POINTS,
                Stat::TOTAL_CENTURY_GAMES,
                Stat::AVERAGE_POINTS_PER_SEASON,
                // Stat::WEEKS_WON_PER_SEASON,
                Stat::AVERAGE_WEEKS_WON_PER_SEASON  
            ],
            $data
        );

        $data['column2'] = $this->statService->getStatsByName(
            [
                Stat::HIGHEST_SEASON_SCORES,
                Stat::LOWEST_SEASON_SCORES
            ],
            $data
        );

        $data['column3'] = $this->statService->getStatsByName(
            [
                Stat::HIGHEST_WEEKLY_SCORES,
                Stat::LOWEST_WEEKLY_SCORES
            ],
            $data
        );

        return view('stats/stats-home', $data);
    }

    public function statsByName(
        ?string $name
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $check = $this->userService->checkUserByName(
            $name
        );
        if (!$check) {
            return redirect(route('stats-home'));
        }
        $data['name'] = ucfirst($name);
        $data['backUrl'] = '/stats';

        $data['userIds'] = [$check];

        $data['column1'] = $this->statService->getStatsByName(
            [
                Stat::TOTAL_SEASONS_WON,
                Stat::TOTAL_WEEKS_WON,
                Stat::TOTAL_SEASON_POINTS,
                Stat::TOTAL_CENTURY_GAMES,
                Stat::AVERAGE_POINTS_PER_SEASON,
                // Stat::WEEKS_WON_PER_SEASON,
                Stat::AVERAGE_WEEKS_WON_PER_SEASON  
            ],
            $data
        );

        $data['column2'] = $this->statService->getStatsByName(
            [
                Stat::HIGHEST_SEASON_SCORES,
                Stat::LOWEST_SEASON_SCORES
            ],
            $data
        );

        $data['column3'] = $this->statService->getStatsByName(
            [
                Stat::HIGHEST_WEEKLY_SCORES,
                Stat::LOWEST_WEEKLY_SCORES
            ],
            $data
        );

        return view('stats/stats-user', $data);
    }
}
