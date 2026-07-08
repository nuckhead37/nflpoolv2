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
use App\Services\ResultService;
use App\Services\PickService;
use App\Services\UserService;
use App\Services\EmailService;
use App\Services\ChampionService;
use App\Services\SettingService;

use App\Models\WeeksPlayed;
use App\Models\Result;

class ResultController extends Controller
{
    private const ENTER_RESULTS_URL = 'enter-results-form-submit';
    private const RECALCULATE_RESULTS_URL = 'recalculate-results-form-submit';

    public function __construct(
        private HelperService $helperService,
        private AdminService $adminService,
        private ScheduleService $scheduleService,
        private ResultService $resultService,
        private PickService $pickService,
        private UserService $userService,
        private EmailService $emailService,
        private ChampionService $championService,
        private settingService $settingService
    )
    {}

    public function enterResults(
        Request $request
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $data['week'] = $this->scheduleService->getCurrentWeek();
        $data['titleType'] = 'Enter ';

        $check = $this->resultService->performValidation(
            $data,
            'enter results',
            Result::INITIAL_RESULTS
        );

        if (!$check) {
            return redirect(route('admin-home'));
        }

        // get the schedule for the week
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $data['week']
        );

        $data['formUrl'] = self::ENTER_RESULTS_URL;

        $data['error'] = $request && $request->session()->pull('error', false);

        return view('admin/enter-results', $data);
    }

    public function postEnterResults(
        Request $request
    ): Redirect {
        $data = $this->helperService->getBasicInfo();
        $data['week'] = $request->has('week') ? $request->week : 0;
        $data['formUrl'] = self::ENTER_RESULTS_URL;
        $data['titleType'] = 'Enter ';

        return $this->resultService->processGamesData(
            $request,
            $data,
            'enter results',
            Result::UPDATE_RESULTS
        );
    }


    public function recalculateResults(): View|Redirect
    {
        $data = $this->helperService->getBasicInfo();
        $data['titleType'] = 'Recalculate ';

        [$canRecalculate, $data['week']] = $this->adminService->canRecalculateResult(
            $data
        );

        if ($canRecalculate !== '') {
            return redirect('/admin');
        }

        $schedules = $this->scheduleService->getScheduleByWeek(
            $data['week']
        );

        $data['games'] = $this->resultService->getGameWinners(
            $schedules
        );

        $data['error'] = '';

        $data['formUrl'] = self::RECALCULATE_RESULTS_URL;

        return view('admin/enter-results', $data);
    }

    public function postRecalculateResults(
        Request $request
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $data['week'] = $request->has('week') ? $request->week : 0;
        $data['formUrl'] = self::RECALCULATE_RESULTS_URL;
        $data['titleType'] = 'Recalculate ';

        return $this->resultService->processGamesData(
            $request,
            $data,
            'update results',
            Result::UPDATE_RESULTS
        );
    }
}
