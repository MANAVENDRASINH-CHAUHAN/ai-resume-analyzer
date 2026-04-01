<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::guard('admin')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid admin email or password.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        ActivityLogger::log(
            'login',
            'Admin Authentication',
            'Admin logged in successfully.',
            null,
            Auth::guard('admin')->id()
        );

        return redirect()->route('admin.dashboard')->with('success', 'Admin login successful.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $adminId = Auth::guard('admin')->id();

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ActivityLogger::log('logout', 'Admin Authentication', 'Admin logged out.', null, $adminId);

        return redirect()->route('admin.login')->with('success', 'Admin session ended.');
    }
}
