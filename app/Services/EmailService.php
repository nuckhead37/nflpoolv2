<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSetup;

class EmailService
{
    public function __construct(
        private UserService $userService
    )
    {

    }

    public function generateWeeklyWinnerEmail(
        array $data,
        array $results
    ): array {

        $data['subject'] = 'NFL Pool ' . $data['week'] . ' Result';
        
        $data['titleText'] = 'Week ' . $data['week'] . ' Result';

        $data['heroImageUrl'] = 'images/week_result.png';

        $data['users'] = [
            ['name' => 'Clive', 'points' => 101],
            ['name' => 'Jim', 'points' => 99]
        ];

        $data['totals'] = [
            [
                'name' => 'Clive',
                'total' => 300,
                'wins' => 3,
                'tied' => 0
            ],
            [
                'name' => 'Jim',
                'total' => 290,
                'wins' => 2,
                'tied' => 1
            ]
        ];

        return $data;
    }

    public function generateSeasonWinnerEmail(
        array $data,
        array $results
    ): array {

        return $data;
    }

    public function sendEmails(
        array $emailData,
        array $users,
        string $template
    ): void {
        foreach ($users as $user) {
            Mail::to($user['email'])->send(new EmailSetup(
                $emailData['users'],
                $emailData['totals'],
                $emailData['subject'],
                $emailData['titleText'],
                $emailData['heroImageUrl'],
                $template
            ));
        }
    }
}
