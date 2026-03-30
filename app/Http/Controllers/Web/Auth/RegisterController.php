<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\SendWelcomeSms;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'member',
        ]);

        Auth::login($user);

        // SMS de bienvenue en async
        SendWelcomeSms::dispatch($user)->onQueue('notifications');

        return redirect()->route('member.dashboard')
            ->with('success', 'Bienvenue sur FitPass ! Votre compte a été créé.');
    }
}
