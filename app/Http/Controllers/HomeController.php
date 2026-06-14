<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;

class HomeController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService
    )
    {
        
    } 

    public function home(): View
    {
        $data = $this->helperService->getBasicInfo();


        return View('home', $data);
    }
}
