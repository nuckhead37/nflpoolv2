<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserService
{
    public function __construct()
    {}

    public function getUserDetails(): ?user {
        return Auth::user();
    }


    public function getUser(): ?User
    {
        return $this->getUserDetails();
    }

}
