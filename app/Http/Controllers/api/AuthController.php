<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input (backend wajib tegas)
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Coba login
        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau password salah'
            ], 401);
        }

        // 3. Login sukses
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'redirect' => match ($user->role) {
                'admin' => '/admin/dashboard',
                'ra' => '/ra/dashboard',
                'supervisor' => '/supervisor/dashboard',
                'manager' => '/manager/dashboard',
                default => '/login',
            }
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
