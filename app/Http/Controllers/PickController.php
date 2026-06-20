<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\ScheduleService;
use App\Services\AdminService;
use App\Services\HelperService;

class PickController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private AdminService $adminService,
        private HelperService $helperService
    )
    {
        
    }

    public function makePicks(
        int $week = 0
    ): View|Redirect {
        /*
            Check the user is logged in and has the make picks permisson


            get the current week

            if $week = 0 then change to be latest week.
            Otherwise if > max week then redirect to home page.

        */
        $data = $this->helperService->getBasicInfo();
        $check = $this->adminService->checkUserAccess(
            'make picks'
        );
        if (!$check) {
            return redirect('/');
        }
        // IS THE SEASON IN ACTION? SIMILAR FUNCTION TO USE ON HOMEPAGE?

        $currentWeek = $this->scheduleService->getCurrentWeek();
        if ($week === 0 || $week < $currentWeek || $week > $data['weeksPerSeason']) {
            return redirect('/');
        }

        // get the schedule for this week
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $week
        );

        if (empty($dat['games'])) {
            return redirect('/');
        }

        // check if the picks have been made

        return View('picks/picks', $data);
        dd('here - ' . $week);
    }

    // public function picks(): View {
    //     $data = [];

    //     $data['games'] = $this->scheduleService->getScheduleByWeek(
    //         1
    //     );
    //     $data['week'] = 1;
    //     return View('picks/picks', $data);
    // }

    // public function picksWeek(int $id): View {
    //     if ($id === 0) {
    //         dd('nothing');
    //     }
    //     $data = [];

    //     $data['games'] = $this->scheduleService->getScheduleByWeek(
    //         $id
    //     );
    //     $data['week'] = $id;
    //     return View('picks/picks', $data);
    // }
}
