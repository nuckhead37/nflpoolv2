<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;
use App\Services\ChampionService;

class HomeController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService,
        private ChampionService $championService
    )
    {
        
    } 

    public function home(): View
    {
        $data = $this->helperService->getBasicInfo();

        if ($data['userLoggedIn']) {
            // get pick info. what's the current week? make current week and future picks
        }

        $data['currentWeek'] = $this->scheduleService->getCurrentWeek();

        $data['seasonInfo'] = [];

        $data['currentChampion'] = $this->championService->getCurrentChampion();

        if ($data['seasonInAction']) {
            // get the season info for display on the home page
        }

        return View('home', $data);
    }
}
