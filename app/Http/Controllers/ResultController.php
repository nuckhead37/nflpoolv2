<?php

declare(strict_type=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;
use App\Services\AdminService;
use App\Services\ScheduleService;


class ResultController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private AdminService $adminService,
        private ScheduleService $scheduleService
    )
    {}

    public function enterResults(): View|Redirect
    {
        $check = $this->adminService->checkUserAccess(
            'enter results'
        );
        if (!$check) {
            return redirect(route('admin-home'));
        }
        $data = $this->helperService->getBasicInfo();

        $data['week'] = $this->scheduleService->getCurrentWeek();

        $validWeek = $this->scheduleService->checkValidWeekForInitialResults(
            $data
        );

        if (!$validWeek) {
            return redirect(route('admin-home'));
        }

        // get the schedule for the week
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $data['week']
        );

        return view('admin/enter-results', $data);
    }
}
