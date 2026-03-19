<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // If no specific roles are required, or the user has one of the required roles, let them pass
        // Admins always have access
        if (empty($roles) || in_array($user->role, $roles) || $user->role === 'admin') {
            return $next($request);
        }

        // Return forbidden or redirect to dashboard
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access that section.');
    }
}
