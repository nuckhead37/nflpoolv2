<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Services\UserService;
use App\Services\helperService;

class UserController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private UserService $userService
    )
    {
        //
    }

    public function account(): View|Redirect
    {
        $data = $this->helperService->getBasicInfo();
        $data['user'] = $this->userService->getUser();
        if (!$data['user']) {
            return redirect('/');
        }

        return View('user/dashboard', $data);
    }
}
