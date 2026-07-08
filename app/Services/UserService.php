<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse as Redirect;

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

    public function checkUserLoggedIn(): bool
    {
        return Auth::user() ? true : false;
    }

    public function getNameById(
        int $id
    ): ?string {
        return User::select('name')
            ->where('id', $id)
            ->first()
            ->name;
    }

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getAllUsersForEmail(): array
    {
        $users = $this->getAllUsers();
        $updatedUsers = [];
        foreach ($users as &$user) {
            $updatedUsers[$user->id]['name'] = $user->name;
            $updatedUsers[$user->id]['email'] = $user->email;
        }
        return $updatedUsers;
    }

    public function checkUserByName(
        ?string $name
    ): ?int {
        return User::select('id')
            ->where(DB::raw('LOWER(name)'), strtolower($name))
            ->first()
            ?->id;
    }

}
