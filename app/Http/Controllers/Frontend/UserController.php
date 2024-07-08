<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Verification;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('frontend/dashboard');
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    public function verification(Request $request)
    {
        $verification = Verification::where('user_id', $request->user()->id)->first();
        $user = $request->user();
        return view('frontend.verification.index', compact('user', 'verification'));
    }

    public function verificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|max:255|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $verification = Verification::where('user_id', $request->user()->id)->first();

        if ($verification) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
            unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);
        }

        $manager = new ImageManager(new Driver());
        // id_front_image
        $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_front_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
        // id_with_face_image
        $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_with_face_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);

        if ($verification) {
            $verification->update([
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'id_front_image' => $id_front_image_name,
                'id_with_face_image' => $id_with_face_image_name,
                'status' => 'Pending',
            ]);

            $notification = array(
                'message' => 'Id Verification request updated successfully.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        Verification::create([
            'user_id' => $request->user()->id,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_front_image' => $id_front_image_name,
            'id_with_face_image' => $id_with_face_image_name,
        ]);

        $notification = array(
            'message' => 'Id Verification request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    // deposit
    public function deposit(Request $request)
    {
        return view('frontend.deposit.index');
    }

    public function depositStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $notification = array(
            'message' => 'Deposit request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    // withdraw
    public function withdraw(Request $request)
    {
        return view('frontend.withdraw.index');
    }

    public function withdrawStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $notification = array(
            'message' => 'Withdraw request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

}
