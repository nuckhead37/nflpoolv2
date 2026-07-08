<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\HelperService;

class StatsController extends Controller
{
    //
    public function __construct(
        private StatService $statService
    )
    {

    }

    public function stats(): View
    {

        dd('stats');
    }
}
