<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\ScheduleService;

class PickController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService
    )
    {
        
    }

    public function picks(): View {
        $data = [];

        $data['games'] = $this->scheduleService->getScheduleByWeek(
            1
        );
        $data['week'] = 1;
        return View('picks', $data);
    }

    public function picksWeek(int $id): View {
        if ($id === 0) {
            dd('nothing');
        }
        $data = [];

        $data['games'] = $this->scheduleService->getScheduleByWeek(
            $id
        );
        $data['week'] = $id;
        return View('picks', $data);
    }
}
