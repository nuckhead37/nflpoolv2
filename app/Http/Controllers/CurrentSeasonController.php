<?php

declare(strict_type=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;
use App\Services\HistoryService;
use App\Services\SettingService;
use App\Services\PickService;
use App\Services\ResultService;

class CurrentSeasonController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private HistoryService $historyService,
        private SettingService $settingService,
        private PickService $pickService,
        private ResultService $resultService
    )
    {}

    public function current(): View
    {
        $data = $this->helperService->getBasicInfo();

        $data['pickWeeks'] = $this->pickService->checkPickOptions(
            $data['userLoggedIn'],
            $data['seasonInAction'],
            $data['weeksPerSeason']
        );

        $data['weekResults'] = $this->resultService->getSeasonResults(
            $data['currentSeason']
        );

        return View('current_season/current_season', $data);
    }

    public function currentWeek(
        int $week = 1
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();

        if ($week < 1 || $week > $data['weeksPerSeason']) {
            return redirect(route('current-season'));
        }

        $data['week'] = $week;

        // get week info
        $data['weekResults'] = $this->resultService->getWeekResults(
            $week
        );
        


        return View('current_season/current_season_week', $data);
    }
}
