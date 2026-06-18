<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;
use App\Services\HistoryService;
use App\Services\SettingService;

class CurrentSeasonController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private HistoryService $historyService,
        private SettingService $settingService
    )
    {}

    public function current(
        int $week = 1
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();

        if ($week < 1 || $week > $data['weeksPerSeason']) {
            return redirect(route('home'));
        }

        dd($week);


        return View('current-season/current-season', $data);
    }
}
