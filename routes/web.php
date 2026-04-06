<?php

use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminGymController;
use App\Http\Controllers\Web\Admin\AdminGymPhotoController;
use App\Http\Controllers\Web\Admin\AdminGymProgramController;
use App\Http\Controllers\Web\Admin\AdminMemberController;
use App\Http\Controllers\Web\Admin\AdminPaymentController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Auth\TwoFactorController;
use App\Http\Controllers\Web\Gym\GymCheckinController;
use App\Http\Controllers\Web\Gym\GymDashboardController;
use App\Http\Controllers\Web\Gym\GymProfileController;
use App\Http\Controllers\Web\Gym\GymProgramOwnerController;
use App\Http\Controllers\Web\Gym\GymScanController;
use App\Http\Controllers\Web\Member\CheckinWebController;
use App\Http\Controllers\Web\Member\DashboardController;
use App\Http\Controllers\Web\Member\GymDirectoryController;
use App\Http\Controllers\Web\Member\MapController;
use App\Http\Controllers\Web\Member\PaymentWebController;
use App\Http\Controllers\Web\Member\ProfilePhotoController;
use App\Http\Controllers\Web\Member\QrCodeController;
use App\Http\Controllers\Web\Member\SubscriptionWebController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\SeoController;
use Illuminate\Support\Facades\Route;

// Landing page publique
Route::get('/', LandingController::class)->name('home');

// PWA
Route::get('/offline', fn () => view('offline'))->name('offline');

// SEO
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt',  [SeoController::class, 'robots'])->name('robots');

// Auth — invités seulement
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'create'])->name('login');
    Route::post('/login',   [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register',[RegisterController::class, 'store'])->name('register.store');
});

Route::get('/logout', [LoginController::class, 'destroy'])
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
        Route::get('/gyms',          [GymDirectoryController::class, 'index'])->name('gyms');
        Route::get('/gyms/{slug}',   [GymDirectoryController::class, 'show'])->name('gyms.show');
        // Photo de profil
        Route::post('/profile/photo',   [ProfilePhotoController::class, 'store'])
            ->middleware('throttle:photo-upload')
            ->name('profile.photo.store');
        Route::delete('/profile/photo', [ProfilePhotoController::class, 'destroy'])
            ->name('profile.photo.destroy');
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

        // Profil salle — modifications par le gym owner
        Route::get('/profil',              [GymProfileController::class, 'edit'])->name('profil');
        Route::put('/profil/infos',        [GymProfileController::class, 'updateInfo'])->name('profil.infos');
        Route::put('/profil/horaires',     [GymProfileController::class, 'updateHours'])->name('profil.horaires');
        Route::put('/profil/activites',    [GymProfileController::class, 'updateActivities'])->name('profil.activites');
        Route::post('/profil/programmes',                   [GymProgramOwnerController::class, 'store'])->name('profil.programmes.store');
        Route::put('/profil/programmes/{program}',          [GymProgramOwnerController::class, 'update'])->name('profil.programmes.update');
        Route::delete('/profil/programmes/{program}',       [GymProgramOwnerController::class, 'destroy'])->name('profil.programmes.destroy');
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

        // Photos salle
        Route::post('/gyms/{gym}/photos',                         [AdminGymPhotoController::class, 'store'])->name('gyms.photos.store');
        Route::delete('/gyms/{gym}/photos/{photo}',              [AdminGymPhotoController::class, 'destroy'])->name('gyms.photos.destroy');
        Route::patch('/gyms/{gym}/photos/{photo}/set-cover',     [AdminGymPhotoController::class, 'setCover'])->name('gyms.photos.cover');

        // Programmes salle
        Route::post('/gyms/{gym}/programs',                      [AdminGymProgramController::class, 'store'])->name('gyms.programs.store');
        Route::put('/gyms/{gym}/programs/{program}',             [AdminGymProgramController::class, 'update'])->name('gyms.programs.update');
        Route::delete('/gyms/{gym}/programs/{program}',          [AdminGymProgramController::class, 'destroy'])->name('gyms.programs.destroy');

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
