<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.user-login');
    }

    public function showRegister(): View
    {
        return view('auth.user-register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        ActivityLogger::log(
            'login',
            'Authentication',
            'User logged in successfully.',
            Auth::guard('web')->id()
        );

        return redirect()->route('user.dashboard')->with('success', 'Login successful.');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => true,
        ]);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        ActivityLogger::log(
            'register',
            'Authentication',
            'New user account created.',
            $user->id
        );

        return redirect()->route('user.dashboard')->with('success', 'Registration successful.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::guard('web')->id();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ActivityLogger::log('logout', 'Authentication', 'User logged out.', $userId);

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
