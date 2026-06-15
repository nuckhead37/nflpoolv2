<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;
use App\Services\HistoryService;
use App\Services\SettingService;

class HistoryController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private HistoryService $historyService,
        private SettingService $settingService
    )
    {}

    public function history(): View
    {
        $data = $this->helperService->getBasicInfo();

        dd($data);

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
            return redirect(route('home'));
        }
        $data['year'] = $year;
        $data['history'] = $this->historyService->getHistoryByYear(
            $year
        );

        dd($data);
        return View('history/history_year', $data);
    }
}
