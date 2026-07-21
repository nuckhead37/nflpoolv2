<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Storage; // REMOVE

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;
use App\Services\ChampionService;
use App\Services\PickService;
use App\Services\resultService;

use App\Models\Result;

class HomeController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService,
        private ChampionService $championService,
        private PickService $pickService,
        private ResultService $resultService
    )
    {
        
    } 

    public function home(): View
    {
        $data = $this->helperService->getBasicInfo();

        $data['pickWeeks'] = $this->pickService->checkPickOptions(
            $data['userLoggedIn'],
            $data['seasonInAction'],
            $data['weeksPerSeason']
        );

        $data['currentWeek'] = $this->scheduleService->getCurrentWeek();

        $data['seasonInfo'] = [];

        $data['currentChampion'] = $this->championService->getCurrentChampion();

        if ($data['seasonInAction']) {
            // get the season info for display on the home page
            $data['seasonCurrentTotals'] = $this->resultService->getCurrentSeasonTotals(
                $data
            );
        }

        return View('home', $data);
    }
}
