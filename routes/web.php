<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\Provider\ProviderJobController;
use App\Http\Controllers\Student\ServiceRequestController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\TicketController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// --- Public ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Auth ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/provider', [AuthController::class, 'showProviderRegister'])->name('register.provider');
    Route::post('/register/provider', [AuthController::class, 'registerProvider']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Student Portal ---
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [ServiceRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [ServiceRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ServiceRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{request}', [ServiceRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{request}/cancel', [ServiceRequestController::class, 'cancel'])->name('requests.cancel');
    Route::post('/requests/{request}/review', [ServiceRequestController::class, 'submitReview'])->name('requests.review');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
});

// --- Admin Reports (protected by Filament's admin access) ---
Route::middleware(['auth'])->prefix('admin-reports')->name('admin.reports.')->group(function () {
    Route::get('/requests-pdf', [ReportController::class, 'requestsPdf'])->name('requests-pdf');
});

// --- Provider Portal ---
Route::middleware(['auth'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jobs', [ProviderJobController::class, 'index'])->name('jobs.index');
    Route::patch('/jobs/{request}/accept', [ProviderJobController::class, 'accept'])->name('jobs.accept');
    Route::patch('/jobs/{request}/decline', [ProviderJobController::class, 'decline'])->name('jobs.decline');
    Route::patch('/jobs/{request}/start', [ProviderJobController::class, 'start'])->name('jobs.start');
    Route::patch('/jobs/{request}/complete', [ProviderJobController::class, 'complete'])->name('jobs.complete');
});
