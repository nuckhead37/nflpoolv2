<?php

declare(strict_type=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;
use Illuminate\Http\JsonResponse;

use App\Services\ScheduleService;
use App\Services\AdminService;
use App\Services\HelperService;
use App\Services\PickService;

class PickController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private AdminService $adminService,
        private HelperService $helperService,
        private PickService $pickService
    )
    {
        
    }

    public function viewPicks(
        int $week = 0
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();

        $currentWeek = $this->scheduleService->getCurrentWeek();
        if ($week < 1 || $week > $currentWeek) {
            return redirect('/current');
        }
        $data['week'] = $week;

        $data['games'] = $this->pickService->getPicksAndScheduleByWeek(
            $week
        );

        // if logged in and has make picks permission
        $data['showMakeEdit'] = $this->pickService->showMakeEdit(
            $data
        );



        // in $data['games'] also get the picks and results - if applicable



        // get schedule for this week

        // get picks for this week

        // if week has been played then get results


        // show all the picks. not editable.

        // BUT HOW TO SHOW CURRENT WEEK TO VIEW ALL THE PICKS??
        return View('picks/view_picks', $data);
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
        $valid = $this->pickService->checkPickAvailable(
            $week,
            $data['weeksPerSeason']
        );
        if (!$valid) {
            return redirect('/current');
        }

        // get the schedule for this week
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $week,
            $data['user']['id']
        );

        if (empty($data['games'])) {
            return redirect('/current');
        }
        $data['week'] = $week;
        $data['totalGames'] = count($data['games']);

        return View('picks/make_picks', $data);
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

    public function adminUpdatePicks(): View|Redirect {
        $check = $this->adminService->checkUserAccess(
            'update picks'
        );
        if (!$check) {
            return redirect(route('admin-home'));
        }
        $data = $this->helperService->getBasicInfo();


        // what week?


        return view('admin/update-picks', $data);
    }

    public function postMakePicks(Request $request): View
    {


        dd('here');
    }
}
