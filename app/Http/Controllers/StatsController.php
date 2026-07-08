<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse as Redirect;
use Illuminate\View\View;

use App\Services\HelperService;
use App\Services\StatsService;
use App\Services\UserService;

use App\Models\Stat;

class StatsController extends Controller
{
    //
    public function __construct(
        private StatsService $statService,
        private HelperService $helperService,
        private UserService $userService
    )
    {
        //
    }

    public function stats(): View
    {
        $data = $this->helperService->getBasicInfo();

        return view('stats/stats-home', $data);
    }

    public function statsByName(
        ?string $name
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $check = $this->userService->checkUserByName(
            $name
        );
        if (!$check) {
            return redirect(route('stats-home'));
        }
        $data['name'] = ucfirst($name);
        $data['backUrl'] = '/stats';

        return view('stats/stats-user', $data);
    }
}
