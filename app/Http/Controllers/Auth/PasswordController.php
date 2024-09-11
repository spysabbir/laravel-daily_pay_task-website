<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['current_password', 'required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ],
        [
            'current_password.current_password' => 'The current password is incorrect.',
            'current_password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'password_confirmation.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'updated_by' => auth()->user()->id,
        ]);

        $notification = array(
            'message' => 'Password updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }
}
