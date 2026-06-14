<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HelperService
{
    public function __construct(
        private UserService $userService
    )
    {

    }

    public function getBasicInfo(): array {
        $data = [];

        $data['user'] = $this->userService->getUser();

        return $data;
    }
}
