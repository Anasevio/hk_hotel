<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        // Belum login
        if (!$user) {
            return redirect()->route('login');
        }

        // Akun nonaktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['username' => 'Akun kamu tidak aktif. Hubungi admin.']);
        }

        // Role tidak sesuai — redirect ke dashboard role sendiri
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return redirect()->route($user->role . '.dashboard');
        }

        return $next($request);
    }
}
