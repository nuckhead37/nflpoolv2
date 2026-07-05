<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;
use App\Services\HistoryService;
use App\Services\SettingService;
use App\Services\ResultService;

class HistoryController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private HistoryService $historyService,
        private SettingService $settingService,
        private ResultService $resultService
    )
    {}

    public function history(): View
    {
        $data = $this->helperService->getBasicInfo();

        $data['years'] = $this->historyService->getAllHistoryYears(
            $data['firstSeason'],
            $data['currentSeason'],
            $data['seasonInAction']
        );

        return View('history/history', $data);
    }

    public function historyByYear(string|int $year = null): View|Redirect
    {
        $data = $this->helperService->getBasicInfo();
        $yearCheck = $this->historyService->validateYear(
            $year,
            $data
        );
        if (!$yearCheck) {
            return redirect(route('history-home'));
        }
        $data['year'] = $year;
        // $data['history'] = $this->historyService->getHistoryByYear(
        //     $year
        // );

        $data['weekResults'] = $this->resultService->getSeasonResultsByYear(
            $year
        );

        return View('history/history_year', $data);
    }
}
