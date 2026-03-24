<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendance;
use App\Http\Controllers\Admin\TimerSettingController;
use App\Http\Controllers\Admin\TaskController as AdminTask;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboard;
use App\Http\Controllers\Supervisor\AttendanceController as SupervisorAttendance;
use App\Http\Controllers\Supervisor\HistoryController as HistoryController;
use App\Http\Controllers\Supervisor\TaskController as SupervisorTask;
use App\Http\Controllers\Supervisor\SpecialCaseController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Manager\AttendanceController as ManagerAttendance;
use App\Http\Controllers\Manager\InspectionController;
use App\Http\Controllers\Ra\DashboardController as RaDashboard;
use App\Http\Controllers\Ra\TaskController as RaTask;
use App\Http\Controllers\Ra\AttendanceController as RaAttendance;
use App\Http\Controllers\Shared\TaskController as RoomController;

// Root → login
Route::get('/', fn() => redirect()->route('login'));

// ── AUTH ──────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── ADMIN ─────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/dashboard',             [AdminDashboard::class,       'index'])->name('dashboard');
    Route::get('/users',                 [UserController::class,       'index'])->name('users.index');
    Route::post('/users',                [UserController::class,       'store'])->name('users.store');
    Route::put('/users/{user}',          [UserController::class,       'update'])->name('users.update');
    Route::post('/tasks/assign',         [AdminTask::class, 'assign'])->name('tasks.assign');
    Route::delete('/tasks/{task}/cancel',[AdminTask::class, 'cancel'])->name('tasks.cancel');
    Route::delete('/users/{user}',       [UserController::class,       'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle', [UserController::class,       'toggleActive'])->name('users.toggle');
    Route::get('/attendance',            [AdminAttendance::class,      'index'])->name('attendance.index');
    Route::get('/attendance/export',     [AdminAttendance::class,      'export'])->name('attendance.export');
    Route::get('/timer',                 [TimerSettingController::class,'index'])->name('timer.index');
    Route::put('/timer/{key}',           [TimerSettingController::class,'update'])->name('timer.update');
    Route::get('/rooms',                 [RoomController::class,       'index'])->name('rooms.index');
    Route::put('/rooms/{room}/status',   [RoomController::class,       'updateStatus'])->name('rooms.status');
    Route::get('/rooms/logs',            [RoomController::class,       'logs'])->name('rooms.logs');
    Route::get('/history',               [HistoryController::class,    'index'])->name('history.index');
});

// ── SUPERVISOR ────────────────────────────────────────────────────
Route::prefix('supervisor')->name('supervisor.')->middleware(['auth','role:supervisor'])->group(function () {
    Route::get('/dashboard',                   [SupervisorDashboard::class, 'index'])->name('dashboard');
    Route::get('/tasks',                       [SupervisorTask::class,      'index'])->name('tasks.index');
    Route::post('/tasks',                      [SupervisorTask::class,      'store'])->name('tasks.store');
    Route::get('/tasks/{task}',                [SupervisorTask::class,      'show'])->name('tasks.show');
    Route::post('/tasks/{task}/approve',       [SupervisorTask::class,      'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/return',        [SupervisorTask::class,      'returnToRa'])->name('tasks.return');
    Route::get('/rooms',                       [RoomController::class,      'index'])->name('rooms.index');
    Route::put('/rooms/{room}/status',         [RoomController::class,      'updateStatus'])->name('rooms.status');
    Route::get('/special-cases',               [SpecialCaseController::class,'index'])->name('special-cases.index');
    Route::post('/special-cases',              [SpecialCaseController::class,'store'])->name('special-cases.store');
    Route::put('/special-cases/{case}',        [SpecialCaseController::class,'update'])->name('special-cases.update');
    Route::post('/special-cases/{case}/resolve',[SpecialCaseController::class,'resolve'])->name('special-cases.resolve');
    route::get('/attendance',                  [SupervisorAttendance::class, 'index'])->name('attendance.index');  
    Route::post('/attendance/checkin',         [SupervisorAttendance::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout',        [SupervisorAttendance::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/history',                     [HistoryController::class,   'index'])->name('history.index');
});

// ── MANAGER ───────────────────────────────────────────────────────
Route::prefix('manager')->name('manager.')->middleware(['auth','role:manager'])->group(function () {
    Route::get('/dashboard',                   [ManagerDashboard::class,  'index'])->name('dashboard');
    Route::get('/inspections',                 [InspectionController::class,'index'])->name('inspections.index');
    Route::get('/inspections/{task}',          [InspectionController::class,'show'])->name('inspections.show');
    Route::post('/inspections/{task}/approve', [InspectionController::class,'approve'])->name('inspections.approve');
    Route::post('/inspections/{task}/return',  [InspectionController::class,'returnToSupervisor'])->name('inspections.return');
    Route::get('/rooms',                       [RoomController::class,    'index'])->name('rooms.index');
    Route::get('/attendance',           [ManagerAttendance::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin',  [ManagerAttendance::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [ManagerAttendance::class, 'checkOut'])->name('attendance.checkout');
 
    Route::get('/history',                     [HistoryController::class, 'index'])->name('history.index');
});

// ── ROOM ATTENDANT ────────────────────────────────────────────────
Route::prefix('ra')->name('ra.')->middleware(['auth','role:ra'])->group(function () {
    Route::get('/dashboard',              [RaDashboard::class,  'index'])->name('dashboard');
    Route::get('/attendance',             [RaAttendance::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in',   [RaAttendance::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out',  [RaAttendance::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/rooms',                  [RoomController::class,'raIndex'])->name('rooms.index');
    Route::get('/rooms/{room}',           [RoomController::class,'raShow'])->name('rooms.show');
    Route::get('/tasks/{task}',           [RaTask::class,        'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start',    [RaTask::class,        'start'])->name('tasks.start');
    Route::post('/tasks/{task}/checklist',[RaTask::class,        'updateChecklist'])->name('tasks.checklist');
    Route::post('/tasks/{task}/submit',   [RaTask::class,        'submit'])->name('tasks.submit');
    Route::get('/history',                [HistoryController::class,'index'])->name('history.index');
});
