<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HelperService
{
    public function __construct(
        private UserService $userService,
        private SettingService $settingService,
        private ScheduleService $scheduleService
    )
    {

    }

    public function getBasicInfo(): array {
        $data = [];

        $data['user'] = $this->userService->getUser();
        $data['firstSeason'] = $this->settingService->getSettingByName('first_season');
        $data['currentSeason'] = $this->settingService->getSettingByName('current_season');
        $data['weeksPerSeason'] = $this->settingService->getSettingByName('weeks_per_season');
        $data['userLoggedIn'] = $this->userService->checkUserLoggedIn();
        $data['seasonInAction'] = $this->scheduleService->checkSeasonInAction();

        return $data;
    }
}
