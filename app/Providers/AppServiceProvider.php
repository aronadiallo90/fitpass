<?php

namespace App\Providers;

use App\Services\CheckinService;
use App\Services\FakePaymentService;
use App\Services\FakeSmsService;
use App\Services\Interfaces\CheckinServiceInterface;
use App\Services\GymSearchService;
use App\Services\Interfaces\GymPhotoServiceInterface;
use App\Services\Interfaces\GymSearchServiceInterface;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\SmsServiceInterface;
use App\Services\Interfaces\SubscriptionServiceInterface;
use App\Services\LocalGymPhotoService;
use App\Services\ProfilePhotoService;
use App\Services\Interfaces\ProfilePhotoServiceInterface;
use App\Services\SubscriptionService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings Services ↔ Interfaces
        // FakePaymentService et FakeSmsService actifs jusqu'à réception des clés API
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->bind(PaymentServiceInterface::class, FakePaymentService::class);
        $this->app->bind(CheckinServiceInterface::class, CheckinService::class);
        $this->app->bind(SmsServiceInterface::class, FakeSmsService::class);
        // Photos : LocalGymPhotoService par défaut — Cloudinary en prod via CLOUDINARY_URL
        $this->app->bind(GymPhotoServiceInterface::class, LocalGymPhotoService::class);
        $this->app->bind(GymSearchServiceInterface::class, GymSearchService::class);
        $this->app->bind(ProfilePhotoServiceInterface::class, ProfilePhotoService::class);
    }

    public function boot(): void
    {
        // Redirection des utilisateurs déjà connectés qui visitent /login
        RedirectIfAuthenticated::redirectUsing(function () {
            return match (Auth::user()->role) {
                'admin', 'super_admin' => route('admin.dashboard'),
                'gym_owner'            => route('gym.dashboard'),
                default                => route('member.dashboard'),
            };
        });

        // Rate limiters nommés — utilisent le cache store (array en tests, Redis en prod)
        RateLimiter::for('api', fn(Request $request) =>
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('auth', fn(Request $request) =>
            Limit::perMinute(10)->by($request->ip())
        );

        RateLimiter::for('admin', fn(Request $request) =>
            Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('checkins', fn(Request $request) =>
            Limit::perMinute(60)->by($request->ip())
        );

        RateLimiter::for('photo-upload', fn(Request $request) =>
            Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
        );
    }
}
