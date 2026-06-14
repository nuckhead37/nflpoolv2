<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\ScheduleService;
use App\Services\UserService;

class HomeController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService
    )
    {
        
    } 

    public function home(): View
    {
        $data = [];


        return View('home', $data);
    }
}
