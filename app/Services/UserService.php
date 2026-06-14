<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct()
    {}

    public function getUserDetails(): ?user {
        $user = Auth::user();
        return $user;
    }
}
