<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $referral_code = $request->query('ref');
        return view('auth.register', ['referral_code' => $referral_code]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', Password::defaults()],
            'terms_conditions' => 'required',
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ],
        [
            'terms_conditions.required' => 'You must agree to the terms and conditions.',
            'referral_code.exists' => 'The referral code is invalid.',
        ]);

        $referrer = $request->referral_code ? User::firstWhere('referral_code', $request->referral_code) : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => Str::random(10),
            'referred_by' => $referrer ? $referrer->id : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
