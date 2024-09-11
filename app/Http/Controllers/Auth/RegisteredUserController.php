<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Notifications\ReferralNotification;

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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'terms_conditions' => 'required',
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'email.regex' => 'The email must be follow the format ****@****.***',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password_confirmation.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'terms_conditions.required' => 'You must agree to the terms and conditions.',
            'referral_code.exists' => 'The referral code is invalid.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Failed to validate captcha response.',
        ]);

        $referrer = $request->referral_code ? User::firstWhere('referral_code', $request->referral_code) : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => Str::random(10),
            'referred_by' => $referrer ? $referrer->id : null,
            'referral_bonus_amount' => $referrer ? get_default_settings('referral_registion_bonus_amount') : 0,
            'withdraw_balance' => $referrer ? get_default_settings('referral_registion_bonus_amount') : 0
        ]);

        if ($referrer) {
            User::where('id', $referrer->id)->update([
                'referral_bonus_amount' => $referrer->referral_bonus_amount + get_default_settings('referral_registion_bonus_amount'),
                'withdraw_balance' => $referrer->withdraw_balance + get_default_settings('referral_registion_bonus_amount')
            ]);

            $referrer = User::where('id', $referrer->id)->first();
            $referrer->notify(new ReferralNotification($referrer, $user));
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
