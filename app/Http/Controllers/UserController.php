<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use App\Services\UserService;
use App\Services\helperService;

class UserController extends Controller
{
    public function __construct(
        private HelperService $helperService,
        private UserService $userService
    )
    {
        //
    }

    public function account(): View|Redirect
    {
        $data = $this->helperService->getBasicInfo();
        $data['user'] = $this->userService->getUser();
        if (!$data['user']) {
            return redirect('/');
        }

        return View('user/account', $data);
    }

    public function update(Request $request)
    {
        $user = $this->userService->getUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist.'
            ]);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        // Only validate the password if one was entered
        if ($request->filled('password')) {
            $rules['password'] = 'confirmed|min:8';
        }

        $validated = $request->validate($rules);

        $update = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only update the password if one was supplied
        if ($request->filled('password')) {
            $update['password'] = Hash::make($validated['password']);
        }

        $user->update($update);

        return response()->json([
            'success' => true,
            'message' => 'record updated successfully.'
        ]);
    }
}
