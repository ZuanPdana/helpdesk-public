<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\TechnicianController as AdminTechnicianController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Technician\NotificationController;
use App\Http\Controllers\Technician\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\TicketCommentController;
use App\Http\Controllers\User\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (true) {
        auth()->user()->isAdmin() => redirect()->route('admin.dashboard'),
        auth()->user()->isTechnician() => redirect()->route('technician.dashboard'),
        default => redirect()->route('user.dashboard'),
    };
});

// Language switcher (available to all — guest and authenticated)
Route::post('/language', [LanguageController::class, 'switch'])
    ->middleware('web')
    ->name('language.switch');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// User Dashboard
Route::middleware(['auth', 'role:user'])
    ->prefix('dashboard')
    ->name('user.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');
        Route::post('tickets/{ticket}/close', [TicketController::class, 'closeTicket'])->name('tickets.close');
        Route::patch('notifications/{notification}/read', [UserNotificationController::class, 'read'])->name('notifications.read');

        Route::get('profile', [UserProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', AdminUserController::class);
        Route::resource('technicians', AdminTechnicianController::class);
        Route::resource('tickets', AdminTicketController::class)->only(['index', 'show']);
        Route::patch('tickets/{ticket}/assignment', [AdminTicketController::class, 'updateAssignment'])->name('tickets.assignment.update');
        Route::patch('tickets/{ticket}/priority', [AdminTicketController::class, 'updatePriority'])->name('tickets.priority.update');
        Route::patch('tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status.update');

        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('notifications.read');
        Route::patch('notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

        Route::get('profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

// Technician area
Route::middleware(['auth', 'role:technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Technician\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tickets', App\Http\Controllers\Technician\TicketController::class)->only(['index', 'show']);
        Route::post('tickets/{ticket}/assign-self', [App\Http\Controllers\Technician\TicketController::class, 'assignSelf'])->name('tickets.assign-self');
        Route::patch('tickets/{ticket}/status', [App\Http\Controllers\Technician\TicketController::class, 'updateStatus'])->name('tickets.status.update');
        Route::post('tickets/{ticket}/comments', [App\Http\Controllers\Technician\TicketCommentController::class, 'store'])->name('tickets.comments.store');
        Route::post('tickets/{ticket}/evidence', [App\Http\Controllers\Technician\TicketController::class, 'uploadEvidence'])->name('tickets.evidence.upload');

        // Technician profile (view and update)
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        // Technician notifications
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    });
