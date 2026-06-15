<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminService
{
    public function __construct(
        private UserService $userService
    )
    {

    }

    public function checkUserAccess(
        string $permission
    ): bool {
        try {
            $user = $this->userService->getUser();
            if (!$user) {
                return false;
            }

            if (!$user->hasPermissionTo($permission)) {
                return false;
            }
        } catch(Exception) {
            return false;
        }
        return true;
    }
}
