<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->withInput($request->only('username'));
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()
                ->withErrors(['username' => 'Akun kamu tidak aktif. Hubungi admin.'])
                ->withInput($request->only('username'));
        }

        $request->session()->regenerate();

        return redirect(match ($user->role) {
            'admin'      => route('admin.dashboard'),
            'supervisor' => route('supervisor.dashboard'),
            'manager'    => route('manager.dashboard'),
            'ra'         => route('ra.dashboard'),
            default      => route('login'),
        });
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
