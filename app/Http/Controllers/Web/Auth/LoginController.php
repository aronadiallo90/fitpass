<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'phone'    => $request->phone,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'phone' => 'Ces identifiants ne correspondent à aucun compte.',
            ])->onlyInput('phone');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            'admin', 'super_admin' => redirect()->route('admin.dashboard'),
            'gym_owner'            => redirect()->route('gym.dashboard'),
            default                => redirect()->route('member.dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
