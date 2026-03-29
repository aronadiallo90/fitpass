<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(private readonly Google2FA $google2fa) {}

    /**
     * Affiche la page de configuration 2FA avec QR code généré.
     */
    public function setup(Request $request): View
    {
        $user = $request->user();

        // Générer et stocker le secret si pas encore fait
        if (empty($user->two_factor_secret)) {
            $secret = $this->google2fa->generateSecretKey();

            // Générer 8 codes de récupération à usage unique
            $recoveryCodes = Collection::times(8, fn() => Str::random(10) . '-' . Str::random(10))
                ->toArray();

            $user->forceFill([
                'two_factor_secret'         => encrypt($secret),
                'two_factor_recovery_codes' => $recoveryCodes,
            ])->save();
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name', 'FitPass'),
            $user->phone,
            $secret
        );

        // Génération locale du QR code SVG (pas d'API externe)
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->generate($qrCodeUrl);

        return view('auth.two-factor-setup', [
            'qrCodeSvg' => $qrCodeSvg,
            'secretKey' => $secret,
        ]);
    }

    /**
     * Confirme la configuration 2FA en vérifiant le premier code TOTP.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $user = $request->user();

        if (empty($user->two_factor_secret)) {
            return back()->withErrors(['code' => 'Aucun secret 2FA configuré.']);
        }

        $valid = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->input('code')
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'Code invalide. Réessayez.']);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        return redirect()->route('admin.dashboard')
            ->with('success', '2FA activé avec succès.');
    }

    /**
     * Vérifie le code TOTP lors d'un challenge (après login admin).
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $user = $request->user();

        $valid = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->input('code')
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'Code invalide. Réessayez.']);
        }

        // Marquer la session 2FA comme vérifiée
        $request->session()->put('two_factor_verified', true);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Authentifie via un code de récupération.
     */
    public function recovery(Request $request): RedirectResponse
    {
        $request->validate([
            'recovery_code' => ['required', 'string'],
        ]);

        $user  = $request->user();
        $codes = $user->two_factor_recovery_codes ?? [];
        $input = $request->input('recovery_code');

        $matchIndex = null;
        foreach ($codes as $index => $code) {
            if (hash_equals($code, $input)) {
                $matchIndex = $index;
                break;
            }
        }

        if ($matchIndex === null) {
            return back()->withErrors(['recovery_code' => 'Code de récupération invalide.']);
        }

        // Invalider le code utilisé
        unset($codes[$matchIndex]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        $request->session()->put('two_factor_verified', true);

        return redirect()->route('admin.dashboard');
    }
}
