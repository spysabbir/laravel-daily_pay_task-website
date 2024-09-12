<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Notifications\ReferralNotification;
use App\Notifications\BonusNotification;

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
        $referralBonus = get_default_settings('referral_registration_bonus_amount');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/'],
            'password' => [
                'required', 'string', 'min:8', 'max:20', 'confirmed',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
            ],
            'terms_conditions' => 'required',
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'email.regex' => 'The email must follow the format " ****@****.*** ".',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'terms_conditions.required' => 'You must agree to the terms and conditions.',
            'referral_code.exists' => 'The referral code is invalid.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Failed to validate captcha response.',
        ]);

        $referrer = optional(User::where('referral_code', $request->referral_code)->first());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => Str::random(10),
            'referred_by' => $referrer->id ?? null,
            'withdraw_balance' => $referrer ? $referralBonus : 0,
        ]);

        if ($referrer) {
            $referrer->increment('withdraw_balance', $referralBonus);

            $referrerBonus = Bonus::create([
                'user_id' => $referrer->id,
                'bonus_by' => $user->id,
                'type' => 'Referral Registration Bonus',
                'amount' => $referralBonus,
            ]);

            $referrer->notify(new BonusNotification($referrerBonus));
            $referrer->notify(new ReferralNotification($referrer, $user));

            $userBonus = Bonus::create([
                'user_id' => $user->id,
                'bonus_by' => $referrer->id,
                'type' => 'Referral Registration Bonus',
                'amount' => $referralBonus,
            ]);

            $user->notify(new BonusNotification($userBonus));
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
