<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register', $this->registrationViewData('candidate'));
    }

    public function showAdminRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register', $this->registrationViewData('admin'));
    }

    public function register(Request $request): RedirectResponse
    {
        return $this->handleRegistration($request, 'candidate');
    }

    public function registerAdmin(Request $request): RedirectResponse
    {
        return $this->handleRegistration($request, 'admin');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('status', 'active')
            ->first();

        $storedPassword = (string) ($user?->password ?? '');

        $passwordMatches = $user && $storedPassword === $validated['password'];

        if (! $passwordMatches) {
            return back()
                ->withErrors(['email' => 'Invalid email, password, or inactive account.'])
                ->withInput($request->only('email'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user())
            ->with('success', 'Login successful.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }

    protected function redirectByRole(?User $user): RedirectResponse
    {
        if ($user?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    protected function handleRegistration(Request $request, string $role): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => $role,
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user)
            ->with('success', ucfirst($role) . ' registration successful. Welcome to AI Resume Analyzer System.');
    }

    protected function registrationViewData(string $role): array
    {
        return [
            'registrationRole' => $role,
            'pageTitle' => $role === 'admin' ? 'Admin Registration' : 'Candidate Registration',
            'pageSubtitle' => $role === 'admin'
                ? 'Create an admin account using the same users table and role-based login system.'
                : 'Create a candidate account to upload resumes and view analysis reports.',
            'submitRoute' => $role === 'admin' ? route('register.admin.submit') : route('register.submit'),
        ];
    }
}
