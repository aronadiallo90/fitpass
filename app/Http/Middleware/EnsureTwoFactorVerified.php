<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass 2FA en local pour les tests manuels
        if (config('app.two_factor_bypass', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return $next($request);
        }

        // Admin sans 2FA configuré → redirection setup
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup');
        }

        // Admin 2FA configuré mais pas encore vérifié dans cette session
        if (!$request->session()->get('two_factor_verified')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
