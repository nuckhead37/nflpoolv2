<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {

    }

    public function home(): View
    {
        $data = [];

        $data['games'] = [
            [
                'id' => 1,
                'home' => 'Raiders',
                'homeId' => 1,
                'away' => 'Dolphins',
                'awayId' => 2
            ],
            [
                'id' => 2,
                'home' => 'Bills',
                'homeId' => 3,
                'away' => 'Jets',
                'awayId' => 4
            ]
        ];
        return View('home', $data);
    }
}
