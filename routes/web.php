<?php

use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminGymController;
use App\Http\Controllers\Web\Admin\AdminMemberController;
use App\Http\Controllers\Web\Admin\AdminPaymentController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Auth\TwoFactorController;
use App\Http\Controllers\Web\Gym\GymCheckinController;
use App\Http\Controllers\Web\Gym\GymDashboardController;
use App\Http\Controllers\Web\Gym\GymScanController;
use App\Http\Controllers\Web\Member\CheckinWebController;
use App\Http\Controllers\Web\Member\DashboardController;
use App\Http\Controllers\Web\Member\MapController;
use App\Http\Controllers\Web\Member\PaymentWebController;
use App\Http\Controllers\Web\Member\QrCodeController;
use App\Http\Controllers\Web\Member\SubscriptionWebController;
use Illuminate\Support\Facades\Route;

// Pages publiques
Route::get('/', fn() => view('welcome'))->name('home');

// Auth — invités seulement
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'create'])->name('login');
    Route::post('/login',   [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register',[RegisterController::class, 'store'])->name('register.store');
});

Route::match(['GET', 'POST'], '/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ── Dashboard membre ────────────────────────────────────
Route::middleware(['auth', 'role:member'])
    ->prefix('dashboard')
    ->name('member.')
    ->group(function () {
        Route::get('/',              DashboardController::class)->name('dashboard');
        Route::get('/my-qrcode',     QrCodeController::class)->name('qrcode');
        Route::get('/subscriptions', [SubscriptionWebController::class, 'index'])->name('subscriptions');
        Route::post('/subscriptions',[SubscriptionWebController::class, 'store'])->name('subscriptions.store');
        Route::get('/payments',      PaymentWebController::class)->name('payments');
        Route::get('/checkins',      CheckinWebController::class)->name('checkins');
        Route::get('/map',           MapController::class)->name('map');
    });

// ── Dashboard gym owner ─────────────────────────────────
Route::middleware(['auth', 'role:gym_owner'])
    ->prefix('gym')
    ->name('gym.')
    ->group(function () {
        Route::get('/',         GymDashboardController::class)->name('dashboard');
        Route::get('/scan',     fn() => view('gym.scan'))->name('scan');
        Route::post('/scan/validate', [GymScanController::class, 'validate'])->name('scan.validate');
        Route::get('/checkins', GymCheckinController::class)->name('checkins');
    });

// ── Dashboard admin (+ 2FA obligatoire) ────────────────
Route::middleware(['auth', 'role:admin,super_admin', '2fa'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/',      AdminDashboardController::class)->name('dashboard');
        Route::get('/members', [AdminMemberController::class, 'index'])->name('members');
        Route::patch('/members/{user}/toggle', [AdminMemberController::class, 'toggle'])->name('members.toggle');
        Route::get('/gyms',          [AdminGymController::class, 'index'])->name('gyms');
        Route::get('/gyms/create',   [AdminGymController::class, 'create'])->name('gyms.create');
        Route::post('/gyms',         [AdminGymController::class, 'store'])->name('gyms.store');
        Route::get('/gyms/{gym}/edit', [AdminGymController::class, 'edit'])->name('gyms.edit');
        Route::put('/gyms/{gym}',    [AdminGymController::class, 'update'])->name('gyms.update');
        Route::patch('/gyms/{gym}/toggle', [AdminGymController::class, 'toggle'])->name('gyms.toggle');
        Route::get('/payments',      AdminPaymentController::class)->name('payments');
    });

// ── Dev only — simulation paiement (local uniquement) ─────────────────
if (app()->isLocal()) {
    Route::middleware('auth')->group(function () {
        Route::post('/dev/pay/{payment}/confirm', [\App\Http\Controllers\Web\Dev\FakePaymentController::class, 'confirm'])
            ->name('dev.pay.confirm');
        Route::post('/dev/pay/{payment}/fail', [\App\Http\Controllers\Web\Dev\FakePaymentController::class, 'fail'])
            ->name('dev.pay.fail');
    });
}

// ── 2FA challenge/setup (admin, sans middleware 2fa pour éviter boucle) ──
Route::middleware(['auth', 'role:admin,super_admin'])
    ->prefix('two-factor')
    ->name('two-factor.')
    ->group(function () {
        Route::get('/setup',    [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('/confirm', [TwoFactorController::class, 'confirm'])->name('confirm');
        Route::get('/challenge',fn() => view('auth.two-factor-challenge'))->name('challenge');
        Route::post('/verify',  [TwoFactorController::class, 'verify'])->name('verify');
        Route::post('/recovery',[TwoFactorController::class, 'recovery'])->name('recovery');
    });
