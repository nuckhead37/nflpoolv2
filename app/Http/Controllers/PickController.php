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
use App\Services\userService;

use App\Models\Pick;

class PickController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private AdminService $adminService,
        private HelperService $helperService,
        private PickService $pickService,
        private UserService $userService
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
        $data['backUrl'] = '/current';

        return View('picks/view_picks', $data);
    }

    public function makePicks(
        int $week = 0,
        Request $request
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $valid = $this->pickService->checkPickAvailable(
            $week,
            $data['weeksPerSeason']
        );
        if (!$valid) {
            return redirect('/current');
        }

        if (!$data['seasonInAction']) {
            return redirect(route('home'));
        }
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $week,
            $data['user']['id']
        );

        if (empty($data['games'])) {
            return redirect('/current');
        }
        $data['week'] = $week;
        $data['totalGames'] = count($data['games']);
    
        $data['success'] = $request && $request->session()->pull('success', false);
        $data['backUrl'] = '/current';

        return View('picks/make_picks', $data);
    }

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

    public function postMakePicks(
        Request $request
    ): Redirect {
        $data = $this->helperService->getBasicInfo();
        $user = $this->userService->getUser();
        if (!$user) {
            return redirect(route('home'));
        }
        $pickData = [];
        $games = $request->input('games');
        $week = $request->input('week');
        $validWeek = $this->scheduleService->checkValidWeek(
            $week,
            $data
        );
        if (!$validWeek) {
            return redirect(route('home'));
        }
        $picks = json_decode($request->input('pickData'));

        foreach ($games as $gameId => $teamId) {
            $pickData[] = [
                'week' => (int) $week,
                'schedule_id' => (int) $gameId,
                'team_id' => (int) $teamId,
                'user_id' => (int) $user->id,
                'points' => (int) $this->pickService->getPickValue(
                    $gameId,
                    $picks
                )
            ];
        }

        $this->pickService->savePickData(
            $pickData
        );

        // send emails

        return redirect('/picks/' . $week)
            ->with([
                'success' => true,
                'backUrl' => '/current'
            ]);
    }
}
