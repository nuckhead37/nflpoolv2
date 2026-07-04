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
        private EmailService $emailService
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

        // $users now contains picks
        // iterate through and compare to $games
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

        if ($data['week'] === $data['weeksPerSeason']) {
            /*
                Season is now over.

                Requires different email

                write champion

                set season_in_action to false


            */
            $emailData = $this->emailService->generateSeasonWinnerEmail(
                $data,
                $results
            );
            $template = 'emails/season-winner';

        } else {
            // normal week

            $emailData = $this->emailService->generateWeeklyWinnerEmail(
                $data,
                $results
            );
            $template = 'emails/weekly-winner';

        }

        $this->emailService->sendEmails(
            $emailData,
            $users,
            $template
        );

        return redirect('/current');

        /*


        write to results table:

        year, user_id, score, winner (1/0), tied (1/0)

        */

        
        dd($results);


        /*

        now get the picks by each player. compare the game ID to a matching key in $games.
        if it matches then check the selected team ID matches val in $games.

        Match then add up the points for the team.

        finally comparing the 2 player results and updateOrCreate in results table. calculate
        who the winner is or if it's tied.

        then send the email unless final week 



        if final week of the season -
            - DO ALL THE ABOVE WITH DIFFERENT EMAIL
            - send champion email with different image
            - insert into champions table (or update?!)
            - set `season_in_action` setting to false
        
        */




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
}
