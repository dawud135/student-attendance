<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('attendance-record', [AttendanceRecordController::class, 'index'])->name('attendance-record.index');
    Route::get('attendance-record/dt', [AttendanceRecordController::class, 'dt'])->name('attendance-record.dt');
    Route::get('attendance-record/create', [AttendanceRecordController::class, 'create'])->name('attendance-record.create');
    Route::post('attendance-record/store', [AttendanceRecordController::class, 'store'])->name('attendance-record.store');
    Route::get('attendance-record/{id}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance-record.edit');
    Route::post('attendance-record/{id}/update', [AttendanceRecordController::class, 'update'])->name('attendance-record.update');

    Route::post('student/search', [StudentController::class, 'search'])->name('student.search');
    Route::post('user/search', [UserController::class, 'search'])->name('user.search');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
