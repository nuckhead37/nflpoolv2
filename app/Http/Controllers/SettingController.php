<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\ScheduleService;
use App\Services\UserService;
use App\Services\HelperService;
use App\Services\AdminService;

class SettingController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private UserService $userService,
        private HelperService $helperService,
        private AdminService $adminService
    )
    {
    }

    public function editSettings(): View|Redirect
    {
        $check = $this->adminService->checkUserAccess(
            'edit settings'
        );
        if (!$check) {
            return redirect(route('admin-home'));
        }

        $data = $this->helperService->getBasicInfo();




        return view('admin/settings', $data);
    }

    public function toggleSeasonInAction(): View
    {
        // should the button click make an ajax request instead?
    }
}
