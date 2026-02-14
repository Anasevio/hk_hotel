<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');

use App\Http\Controllers\RA\DashboardController;

Route::middleware(['auth'])->prefix('ra')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('ra.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/absensi', function () {
    return 'Halaman Absensi';
})->name('absensi');

Route::get('/room', function () {
    return 'Halaman Room';
})->name('room');

Route::get('/riwayat', function () {
    return 'Halaman Riwayat';
})->name('riwayat');

use Illuminate\Support\Facades\Auth;

Route::post('/logout-web', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Berhasil logout');
})->name('logout.web');

use App\Http\Controllers\RA\AbsensiController;

Route::middleware(['auth'])->prefix('ra')->name('ra.')->group(function () {
    
    Route::get('/absensi', [AbsensiController::class, 'index'])
        ->name('absensi');

    Route::post('/absensi', [AbsensiController::class, 'store'])
        ->name('absensi.store');
});

