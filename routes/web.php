<?php

use App\Http\Controllers\AlumnusController;
use App\Http\Controllers\OutreachController;
use App\Http\Controllers\RedemptionWeekAttendanceController;
use App\Http\Controllers\RedemptionWeekSessionController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\TenureController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return \Inertia\Inertia::render('public/Landing', [
        'stats' => [
            'totalAlumni' => \App\Models\Alumnus::count(),
            'totalTenures' => \App\Models\Tenure::count(),
            'activeYear' => (int) date('Y') - 1985, // Fellowship started ~1985
        ],
    ]);
})->name('home.landing');

// Public Alumni Portal (no auth required)
Route::prefix('portal')->group(function () {
    Route::get('/', [\App\Http\Controllers\AlumnusPortalController::class, 'index'])->name('portal.index');
    Route::post('/lookup', [\App\Http\Controllers\AlumnusPortalController::class, 'lookup'])->name('portal.lookup');
    Route::post('/submit', [\App\Http\Controllers\AlumnusPortalController::class, 'store'])->name('portal.store');
    Route::post('/update/{alumnus}', [\App\Http\Controllers\AlumnusPortalController::class, 'update'])->name('portal.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // ... existing admin routes ...

        // Pending Alumni Updates
        Route::get('/admin/pending-updates', [\App\Http\Controllers\PendingAlumnusUpdateController::class, 'index'])->name('admin.pending-updates.index');
        Route::post('/admin/pending-updates/{update}/approve', [\App\Http\Controllers\PendingAlumnusUpdateController::class, 'approve'])->name('admin.pending-updates.approve');
        Route::post('/admin/pending-updates/{update}/reject', [\App\Http\Controllers\PendingAlumnusUpdateController::class, 'reject'])->name('admin.pending-updates.reject');

        Route::get('/analytics/outreach', [OutreachController::class, 'index'])->name('analytics.outreach');

        Route::resource('tenures', TenureController::class)->except(['show', 'destroy', 'edit', 'create']);
        Route::resource('departments', \App\Http\Controllers\DepartmentController::class)->except(['show', 'edit', 'create']);

        Route::get('alumni/distribution', [AlumnusController::class, 'distribution'])->name('alumni.distribution');
        Route::get('alumni/executives', [AlumnusController::class, 'executives'])->name('alumni.executives');
        Route::get('alumni/export', [AlumnusController::class, 'export'])->name('alumni.export');
        Route::post('alumni/import', [AlumnusController::class, 'importStore'])->name('alumni.import.store');

        Route::post('alumni', [AlumnusController::class, 'store'])->name('alumni.store');

        Route::put('alumni/{alumnus}', [AlumnusController::class, 'update'])->name('alumni.update');
        Route::delete('alumni/{alumnus}', [AlumnusController::class, 'destroy'])->name('alumni.destroy');
        Route::delete('communications/{log}', [\App\Http\Controllers\CommunicationLogController::class, 'destroy'])->name('communications.destroy');

        // User Management (admin only)
        Route::get('admin/users', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index');
        Route::post('admin/users', [\App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store');
        Route::put('admin/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('admin/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy');

        // Redemption Week - Admin only (session management)
        Route::post('redemption-week', [RedemptionWeekSessionController::class, 'store'])->name('redemption-week.store');
        Route::put('redemption-week/{session}', [RedemptionWeekSessionController::class, 'update'])->name('redemption-week.update');
        Route::delete('redemption-week/{session}', [RedemptionWeekSessionController::class, 'destroy'])->name('redemption-week.destroy');
        Route::delete('redemption-week/{session}/attendance/{attendance}', [RedemptionWeekAttendanceController::class, 'destroy'])->name('redemption-week.attendance.destroy');
        Route::post('redemption-week/{session}/attendance/bulk-destroy', [RedemptionWeekAttendanceController::class, 'bulkDestroy'])->name('redemption-week.attendance.bulk-destroy');
    });

    // Member routes (all authenticated users)
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('alumni', [AlumnusController::class, 'index'])->name('alumni.index');
    Route::get('alumni/birthdays', [AlumnusController::class, 'birthdays'])->name('alumni.birthdays');
    Route::get('alumni/{alumnus}', [AlumnusController::class, 'show'])->name('alumni.show');
    Route::post('alumni/{alumnus}/communications', [\App\Http\Controllers\CommunicationLogController::class, 'store'])->name('alumni.communications.store');

    // Duplicate management (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('alumni/duplicates/detect', [\App\Http\Controllers\AlumnusDuplicateController::class, 'index'])->name('alumni.duplicates');
        Route::post('alumni/duplicates/dismiss', [\App\Http\Controllers\AlumnusDuplicateController::class, 'dismiss'])->name('alumni.duplicates.dismiss');
        Route::post('alumni/{alumnus}/merge/{target}', [\App\Http\Controllers\AlumnusDuplicateController::class, 'merge'])->name('alumni.merge');
    });

    // Redemption Week - Member routes (all authenticated users)
    Route::get('redemption-week', [RedemptionWeekSessionController::class, 'index'])->name('redemption-week.index');
    Route::get('redemption-week/{session}', [RedemptionWeekSessionController::class, 'show'])->name('redemption-week.show');
    Route::post('redemption-week/{session}/attendance', [RedemptionWeekAttendanceController::class, 'store'])->name('redemption-week.attendance.store');

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

    // Admin-only settings routes
    Route::middleware('admin')->group(function () {
        Route::get('settings/backup', [\App\Http\Controllers\Settings\BackupController::class, 'index'])->name('backup.index');
        Route::post('settings/backup', [\App\Http\Controllers\Settings\BackupController::class, 'store'])->name('backup.store');
        Route::get('settings/backup/download/{filename}', [\App\Http\Controllers\Settings\BackupController::class, 'download'])->name('backup.download');

        Route::get('settings/calendar', [\App\Http\Controllers\Settings\CalendarController::class, 'index'])->name('calendar.index');
        Route::post('settings/calendar/sync', [\App\Http\Controllers\Settings\CalendarController::class, 'sync'])->name('calendar.sync');
        Route::post('settings/calendar/unsync', [\App\Http\Controllers\Settings\CalendarController::class, 'unsync'])->name('calendar.unsync');
    });
});
