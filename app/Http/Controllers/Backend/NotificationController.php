<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {
        $allUser = User::where('user_type', 'Frontend')->whereIn('status', ['Active', 'Blocked'])->get();
        $allEmployee = User::where('user_type', 'Backend')->where('status', 'Active')->get();
        return view('backend.notification.index', compact('allUser', 'allEmployee'));
    }

    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'user_id' => [
                'required_if:type,Single Employee',
                'required_if:type,Single User'
            ],
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ],[
            'user_id.required_if' => 'The name field is required when type is Single Employee or Single User.',

        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        } else {
            // Your logic for sending notifications
            return response()->json([
                'status' => 200,
                'message' => 'Notification sent successfully!'
            ]);
        }
    }

}
