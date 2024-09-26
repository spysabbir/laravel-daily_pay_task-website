<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function passwordReset(Request $request): View
    {
        return view('frontend.auth.reset-password', ['request' => $request]);
    }

    public function backendPasswordReset(Request $request): View
    {
        return view('backend.auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ],
        [
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password_confirmation.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            $user = User::where('email', $request->email)->first();
            if ($user->user_type == 'Frontend') {
                return redirect()->route('login')->with('status', __($status));
            } else {
                return redirect()->route('backend.login')->with('status', __($status));
            }
        } else {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }
    }
}
