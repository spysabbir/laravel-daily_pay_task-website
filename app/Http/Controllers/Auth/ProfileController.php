<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,'.$request->user()->id, 'regex:/^[a-z0-9]+$/'],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string']
        ],
        [
            'username.regex' => 'The username can only contain lowercase letters and numbers.',
            'phone.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        $request->user()->update([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'bio' => $request->bio,
            'updated_by' => auth()->user()->id,
        ]);

        if($request->hasFile('profile_photo')){
            if($request->user()->profile_photo != 'default_profile_photo.png'){
                unlink(base_path("public/uploads/profile_photo/").$request->user()->profile_photo);
            }
            $manager = new ImageManager(new Driver());
            $profile_photo_name = $request->user()->id."-Profile-Photo".".". $request->file('profile_photo')->getClientOriginalExtension();
            $image = $manager->read($request->file('profile_photo'));
            $image->toJpeg(80)->save(base_path("public/uploads/profile_photo/").$profile_photo_name);
            $request->user()->update([
                'profile_photo' => $profile_photo_name
            ]);
        }

        $notification = array(
            'message' => 'Profile updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'account_password' => ['required', 'string', 'min:8', 'max:20', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'current_password'],

        ],
        [
            'account_password.current_password' => 'The password is incorrect.',
            'account_password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ]);

        $user = $request->user();

        $user->updated_by = auth()->user()->id;
        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login')->with('status', 'Your account has been deleted successfully.');
    }
}
