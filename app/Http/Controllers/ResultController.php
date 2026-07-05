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
use App\Services\settingService;

use App\Models\WeeksPlayed;

class ResultController extends Controller
{
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

        $check = $this->resultService->performValidation(
            $data,
            'enter results',
            'initial_results'
        );

        if (!$check) {
            return redirect(route('admin-home'));
        }

        // get the schedule for the week
        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $data['week']
        );

        $data['error'] = $request && $request->session()->pull('error', false);

        return view('admin/enter-results', $data);
    }

    public function postEnterResults(
        Request $request
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $data['week'] = $this->scheduleService->getCurrentWeek();

        $check = $this->resultService->performValidation(
            $data,
            'enter results',
            'initial_results'
        );

        if (!$check) {
            return redirect('/enter-results')
                ->with('error', true);
        }

        if (!$request->has('games')) {
            return redirect('/enter-results')
            ->with('error', true);
        }

        $games = $request->games;

        $scheduleIds = array_keys($games);

        // check that the games all match the presented data.
        $validateGames = $this->scheduleService->validateGames(
            $data,
            $scheduleIds
        );

        if (!$validateGames) {
            return redirect('/enter-results')
                ->with('error', true);
        }

        // write the games
        $this->resultService->enterGameResults(
            $games
        );

        $users = $this->userService->getAllUsers();

        $users = $this->pickService->getPicksByScheduleIdsForUsers(
            $games,
            $users
        );

        $results = $this->resultService->calculateUserTotalForWeek(
            $games,
            $users,
            $data['week'],
            $data['currentSeason']
        );

        $results = $this->resultService->calculateWinner(
            $results
        );

        $this->scheduleService->addWeekPlayed(
            $data['week']
        );

        $totals = $this->resultService->calculateSeasonTotals(
            $data['currentSeason']
        );

        if ($data['week'] === $data['weeksPerSeason']) {
            $champion = $this->championService->getChampion(
                $totals
            );

            $this->championService->createChampionRecord(
                $data['currentSeason'],
                $champion
            );

            $this->settingService->updateSettingByName(
                'season_in_action',
                false
            );
    
            $emailData = $this->emailService->generateSeasonWinnerEmail(
                $data,
                $results,
                $totals,
                $champion
            );
            $template = 'emails/season-winner';
        } else {
            // normal week
            $emailData = $this->emailService->generateWeeklyWinnerEmail(
                $data,
                $results,
                $totals
            );
            $template = 'emails/weekly-winner';
        }

        $this->emailService->sendEmails(
            $emailData,
            $users,
            $template
        );

        return redirect('/current');
    }

    public function postUpdateResults(
        Request $request
    ): View|Redirect {
        $data = $this->helperService->getBasicInfo();
        $data['week'] = $request->week ?? 0;

        /*
            The results already exist in the database.
            
            The week should be in the request payload.
            
            Need to check that the week has been played, and it is the week before the current week.

            Perform a lot of the same actions.


        */

        $check = $this->resultService->performValidation(
            $data,
            'update results',
            'update_results'
        );

        if (!$check) {
            return redirect('/update-results')
                ->with('error', true);
        }

        $data['week'] = $this->scheduleService->getCurrentWeek();
    }

    public function recalculateResults(): View|Redirect
    {
        // check season in action
        // get last week played. if it's still week 1 then don't allow

        // if season is not in action, check weeks played table. if in there and
        // current season value is the last entry in results table then allow final week
        // recalculation.



    }
}
