<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\ReferralRegistrationNotification;
use App\Notifications\BonusNotification;

class RegisteredUserController extends Controller
{
    public function register(Request $request)
    {
        $referral_code = $request->query('ref');
        return view('frontend.auth.register', ['referral_code' => $referral_code]);
    }

    public function store(Request $request)
    {
        $referrer = User::where('referral_code', $request->referral_code)->where('user_type', 'Frontend')->first();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/'],
            'password' => [
                'required', 'string', 'min:8', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'confirmed'
            ],
            'password_confirmation' => [
                'required', 'string', 'min:8', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
            ],
            'terms_conditions' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'email.regex' => 'The email must follow the format " ****@****.*** ".',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password_confirmation.regex' => 'The confirm password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'terms_conditions.required' => 'You must agree to the terms and conditions.',
            'referral_code.exists' => 'The referral code is invalid.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Failed to validate captcha response.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referral_code' => Str::random(12),
            'referred_by' => $referrer ? $referrer->id : null,
        ]);

        $referralBonus = get_default_settings('referral_registration_bonus_amount');
        if ($request->referral_code) {
            $user->update(['withdraw_balance' => $referralBonus]);

            $userBonus = Bonus::create([
                'user_id' => $user->id,
                'bonus_by' => $referrer->id,
                'type' => 'Referral Registration Bonus',
                'amount' => $referralBonus,
            ]);

            $user->notify(new ReferralRegistrationNotification($referrer, $user));
            $user->notify(new BonusNotification($userBonus));
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
