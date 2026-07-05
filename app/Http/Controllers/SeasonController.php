<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;
use App\Services\AdminService;

class SeasonController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService,
        private AdminService $adminService
    )
    {
    }

    public function createSeason(): View|Redirect
    {
        $check = $this->adminService->checkUserAccess(
            'create season'
        );
        if (!$check) {
            return redirect(route('admin-home'));
        }

        $data = $this->helperService->getBasicInfo();

        /*

        check all is in a condition to create a new season.

        does weeks_played contain all the weeks?

        does champions contain the current season?

        

        if all is good:

            - truncate games_played
            - increment current season value
            - set season in action flag to false
            - truncate picks
            - truncate game_results


        */


        return view('admin/create-season', $data);
    }
}
