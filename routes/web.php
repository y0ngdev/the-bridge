<?php

use App\Http\Controllers\AlumnusController;
use App\Http\Controllers\OutreachController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\TenureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::middleware(['guest'])->get('/', function (Request $request) {
    return Inertia::render(
        'Welcome',
        [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'status' => $request->session()->get('status'),
        ]
    );
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Admin-only routes


    Route::middleware('admin')->group(function () {
        Route::get('/analytics/outreach', [OutreachController::class, 'index'])->name('analytics.outreach');

        Route::resource('tenures', TenureController::class)->except(['show', 'destroy', 'edit', 'create']);
        Route::resource('departments', \App\Http\Controllers\DepartmentController::class)->except(['show', 'edit', 'create']);

        Route::get('alumni/distribution', [AlumnusController::class, 'distribution'])->name('alumni.distribution');
        Route::get('alumni/executives', [AlumnusController::class, 'executives'])->name('alumni.executives');
        Route::get('alumni/export', [AlumnusController::class, 'export'])->name('alumni.export');
        Route::post('alumni/import', [AlumnusController::class, 'importStore'])->name('alumni.import.store');
        Route::get('alumni/create', [AlumnusController::class, 'create'])->name('alumni.create');
        Route::post('alumni', [AlumnusController::class, 'store'])->name('alumni.store');
        Route::get('alumni/{alumnus}/edit', [AlumnusController::class, 'edit'])->name('alumni.edit');
        Route::put('alumni/{alumnus}', [AlumnusController::class, 'update'])->name('alumni.update');
        Route::delete('alumni/{alumnus}', [AlumnusController::class, 'destroy'])->name('alumni.destroy');
        Route::delete('communications/{log}', [\App\Http\Controllers\CommunicationLogController::class, 'destroy'])->name('communications.destroy');
    });

    // Member routes (all authenticated users)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('alumni', [AlumnusController::class, 'index'])->name('alumni.index');
    Route::get('alumni/birthdays', [AlumnusController::class, 'birthdays'])->name('alumni.birthdays');
    Route::get('alumni/{alumnus}', [AlumnusController::class, 'show'])->name('alumni.show');
    Route::post('alumni/{alumnus}/communications', [\App\Http\Controllers\CommunicationLogController::class, 'store'])->name('alumni.communications.store');



});

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('settings/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('settings/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/backup', [\App\Http\Controllers\Settings\BackupController::class, 'index'])->name('backup.index');
    Route::post('settings/backup', [\App\Http\Controllers\Settings\BackupController::class, 'store'])->name('backup.store');
    Route::get('settings/backup/download/{filename}', [\App\Http\Controllers\Settings\BackupController::class, 'download'])->name('backup.download');

    Route::get('settings/calendar', [\App\Http\Controllers\Settings\CalendarController::class, 'index'])->name('calendar.index');
    Route::post('settings/calendar/sync', [\App\Http\Controllers\Settings\CalendarController::class, 'sync'])->name('calendar.sync');
});
