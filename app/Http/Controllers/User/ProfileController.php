<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('user.profile', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_summary' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        ActivityLogger::log('update', 'Profile', 'User profile updated.', $user->id);

        return back()->with('success', 'Profile updated successfully.');
    }
}
