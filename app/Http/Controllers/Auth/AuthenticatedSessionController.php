<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            if (Auth::user()->user_type === 'Backend') {
                return redirect()->route('backend.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('frontend.auth.login');
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::updateOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'password' => Hash::make(Str::random(8)),
            'email_verified_at' => now(),
            'referral_code' => Str::random(12),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ]);

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public function backendLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->user_type === 'Backend') {
                return redirect()->route('backend.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('backend.auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()->user_type === 'Backend') {
            return redirect()->intended(route('backend.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request)
    {
        $url = $request->user()->user_type === 'Backend' ? route('backend.login', absolute: false) : route('index', absolute: false);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($url);
    }
}
