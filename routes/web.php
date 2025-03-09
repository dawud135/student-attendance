<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AttendanceRecordController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('attendance-record', [AttendanceRecordController::class, 'index'])->name('attendance-record');
    Route::get('attendance-record/dt', [AttendanceRecordController::class, 'dt'])->name('attendance-record.dt');
    Route::get('attendance-record/create', [AttendanceRecordController::class, 'create'])->name('attendance-record.create');
    Route::post('attendance-record', [AttendanceRecordController::class, 'store'])->name('attendance-record.store');
    Route::get('attendance-record/{id}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance-record.edit');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
