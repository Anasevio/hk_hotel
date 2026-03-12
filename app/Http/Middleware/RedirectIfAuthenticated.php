<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $route = match ($user->role) {
                    'admin'      => route('admin.dashboard'),
                    'supervisor' => route('supervisor.dashboard'),
                    'manager'    => route('manager.dashboard'),
                    'ra'         => route('ra.dashboard'),
                    default      => '/',
                };
                return redirect($route);
            }
        }

        return $next($request);
    }
}