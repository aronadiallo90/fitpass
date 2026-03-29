<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Pages publiques
Route::get('/', fn() => view('welcome'))->name('home');

// Auth — invités seulement
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Dashboard membre
Route::middleware(['auth', 'role:member'])
    ->prefix('dashboard')
    ->name('member.')
    ->group(function () {
        Route::get('/', fn() => view('member.dashboard'))->name('dashboard');
        Route::get('/my-qrcode', fn() => view('member.qrcode'))->name('qrcode');
        Route::get('/subscriptions', fn() => view('member.subscriptions'))->name('subscriptions');
        Route::get('/payments', fn() => view('member.payments'))->name('payments');
        Route::get('/checkins', fn() => view('member.checkins'))->name('checkins');
    });

// Dashboard gym owner
Route::middleware(['auth', 'role:gym_owner'])
    ->prefix('gym')
    ->name('gym.')
    ->group(function () {
        Route::get('/', fn() => view('gym.dashboard'))->name('dashboard');
        Route::get('/scan', fn() => view('gym.scan'))->name('scan');
        Route::get('/checkins', fn() => view('gym.checkins'))->name('checkins');
    });

// Dashboard admin (+ 2FA obligatoire)
Route::middleware(['auth', 'role:admin,super_admin', '2fa'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('/members', fn() => view('admin.members'))->name('members');
        Route::get('/gyms', fn() => view('admin.gyms'))->name('gyms');
    });

// 2FA challenge/setup (admin uniquement, sans 2fa middleware pour éviter boucle)
Route::middleware(['auth', 'role:admin,super_admin'])
    ->prefix('two-factor')
    ->name('two-factor.')
    ->group(function () {
        Route::get('/setup', fn() => view('auth.two-factor-setup'))->name('setup');
        Route::get('/challenge', fn() => view('auth.two-factor-challenge'))->name('challenge');
    });
