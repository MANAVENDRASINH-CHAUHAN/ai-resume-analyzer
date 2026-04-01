<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobRoleController as AdminJobRoleController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Admin\ResumeManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register/admin', [AuthController::class, 'showAdminRegister'])->name('register.admin');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/register/admin', [AuthController::class, 'registerAdmin'])->name('register.admin.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

Route::middleware(['auth', 'candidate'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [UserDashboardController::class, 'stats'])->name('dashboard.stats');
    Route::view('/profile', 'user.profile')->name('profile');
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/upload', [ResumeController::class, 'create'])->name('resumes.create');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::get('/resumes/status-list', [ResumeController::class, 'statusList'])->name('resumes.status-list');
    Route::get('/resumes/{resume}/download', [ResumeController::class, 'download'])->name('resumes.download');
    Route::post('/resumes/{resume}/analyze', [ResumeController::class, 'analyze'])->name('resumes.analyze');
    Route::get('/resumes/{resume}/status', [ResumeController::class, 'status'])->name('resumes.status');
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{resume}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{resume}/print', [ReportController::class, 'print'])->name('reports.print');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

    Route::get('/job-roles', [AdminJobRoleController::class, 'index'])->name('job-roles.index');
    Route::get('/job-roles/create', [AdminJobRoleController::class, 'create'])->name('job-roles.create');
    Route::post('/job-roles', [AdminJobRoleController::class, 'store'])->name('job-roles.store');
    Route::get('/job-roles/{jobRole}/edit', [AdminJobRoleController::class, 'edit'])->name('job-roles.edit');
    Route::put('/job-roles/{jobRole}', [AdminJobRoleController::class, 'update'])->name('job-roles.update');
    Route::delete('/job-roles/{jobRole}', [AdminJobRoleController::class, 'destroy'])->name('job-roles.destroy');

    Route::get('/resumes', [ResumeManagementController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/{resume}', [ResumeManagementController::class, 'show'])->name('resumes.show');
    Route::get('/resumes/{resume}/download', [ResumeManagementController::class, 'download'])->name('resumes.download');
    Route::put('/resumes/{resume}/status', [ResumeManagementController::class, 'updateStatus'])->name('resumes.update-status');
    Route::delete('/resumes/{resume}', [ResumeManagementController::class, 'destroy'])->name('resumes.destroy');

    Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports.index');
    Route::get('/reports/{resume}', [ReportManagementController::class, 'show'])->name('reports.show');
});
