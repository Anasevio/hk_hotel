<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RA\DashboardController;
use App\Http\Controllers\RA\AbsensiController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| PROFILE (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| RA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('ra')->name('ra.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/absensi', [AbsensiController::class, 'index'])
        ->name('absensi');

    Route::post('/absensi', [AbsensiController::class, 'store'])
        ->name('absensi.store');

    Route::get('/room', function () {
        return view('RA.room');
    })->name('room');

    Route::get('/riwayat', function () {
        return view('RA.riwayat');
    })->name('riwayat');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard_admin');
    })->name('dashboard');

    Route::get('/history', function () {
        return view('admin.history_admin');
    })->name('history');

    Route::get('/users', [AdminDashboardController::class, 'users'])
        ->name('users');

    Route::post('/user/{user}/role', [AdminDashboardController::class, 'updateUserRole'])
        ->name('user.updateRole');

    Route::get('/tugas', function () {
        return view('admin.tugas_admin', [
            'selectedUser' => \App\Models\User::find(request('user'))
        ]);
    })->name('tugas');

    Route::post('/tugas', [AdminDashboardController::class, 'storeTugas'])
        ->name('tugas.store');
});

/*
|--------------------------------------------------------------------------
| LOGOUT CUSTOM
|--------------------------------------------------------------------------
*/
Route::post('/logout-web', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Berhasil logout');
})->name('logout.web');
