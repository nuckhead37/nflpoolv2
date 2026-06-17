<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;
use App\Services\AdminService;

class AdminController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService,
        private AdminService $adminService
    )
    {
    }

    public function adminHome(): View|Redirect
    {
        $check = $this->adminService->checkUserAccess(
            'use admin'
        );
        if (!$check) {
            return redirect('/');
        }

        $data = $this->helperService->getBasicInfo();

        return view('admin/admin', $data);
    }

    public function adminEnterResults(): View|Redirect
    {

    }

    public function adminUpdatePicks(): View|Redirect
    {

    }

    public function adminCreateSeason(): View|Redirect
    {

    }

    public function adminEditSettings(): View|Redirect
    {

    }

    public function adminManageUsers(): View|Redirect
    {

    }
}
