<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSetup;
use App\Models\User;

class EmailService
{
    public function __construct(
        private UserService $userService
    )
    {
        //
    }

    public function generateWeeklyWinnerEmail(
        array $data,
        array $results,
        array $totals
    ): array {
        $data['subject'] = 'NFL Pool ' . $data['week'] . ' Result';
        
        $data['titleText'] = 'Week ' . $data['week'] . ' Result';

        $data['heroImageUrl'] = 'images/week_result.png';

        $data['users'] = [];

        foreach ($results as $result) {
            $data['users'][] = [
                'name' => $result['name'],
                'points' => $result['total']
            ];
        }
        $data['totals'] = $totals;

        return $data;
    }

    public function generateSeasonWinnerEmail(
        array $data,
        array $results,
        array $totals,
        array $champion
    ): array {
        $data['subject'] = 'NFL Pool ' . $data['week'] . ' Result';
        
        $data['titleText'] = 'Week ' . $data['week'] . ' Result';
        $data['championText'] = $data['currentSeason'] . ' Champion: ' . $champion['champion'];

        $data['heroImageUrl'] = $champion['image'];

        $data['users'] = [];

        foreach ($results as $result) {
            $data['users'][] = [
                'name' => $result['name'],
                'points' => $result['total']
            ];
        }
        $data['totals'] = $totals;

        return $data;
    }

    public function sendEmails(
        array $emailData,
        array $users,
        string $template
    ): void {
        foreach ($users as $user) {
            Mail::to($user['email'])->send(new EmailSetup(
                $emailData['users'] ?? [],
                $emailData['totals'] ?? [],
                $emailData['subject'],
                $emailData['titleText'],
                $emailData['championText'] ?? '',
                $emailData['heroImageUrl'],
                $emailData['picks'] ?? [],
                $template
            ));
        }
    }

    public function generatePickEmail(
        array $data,
        array $pickData,
        User $user
    ): array {
        $data['subject'] = 'NFL Pool ' . $data['week'] . ' Picks For ' . $user->name;
        
        $data['titleText'] = 'Week ' . $data['week'] . ' Picks For ' . $user->name;

        $data['heroImageUrl'] = 'images/week_picks.png';

        $data['picks'] = $pickData;

        return $data;
    }
}
