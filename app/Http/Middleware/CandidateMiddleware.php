<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CandidateMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'candidate') {
            if ($user && $user->role === 'admin') {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'You cannot access the candidate dashboard.');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
