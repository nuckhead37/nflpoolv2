<?php

declare(strict_type=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse as Redirect;

use App\Services\HelperService;

class ResultController extends Controller
{
    public function __construct(
        private HelperService $helperService
    )
    {}

    public function enterResults(): View|Redirect
    {
        $check = $this->adminService->checkUserAccess(
            'enter reults'
        );
        if (!$check) {
            return redirect(route('admin-home'));
        }
        $data = $this->helperService->getBasicInfo();


        // what week?


        return view('admin/enter-results', $data);
    }
}
