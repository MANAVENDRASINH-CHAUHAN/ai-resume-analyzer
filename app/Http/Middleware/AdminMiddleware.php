<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            if ($user && $user->role === 'candidate') {
                return redirect()
                    ->route('user.dashboard')
                    ->with('error', 'You cannot access the admin dashboard.');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
