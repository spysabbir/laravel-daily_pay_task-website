<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use App\Models\Support;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Events\SupportEvent;


class SupportController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('support.index') , only:['support', 'getUserSupports', 'getSearchSupportUser']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('support.reply') , only:['supportSendMessageReply']),
        ];
    }

    public function support()
    {
        $users = User::where('user_type', 'Frontend')->where('status', 'Active')->get();
        $unreadSupportUserIds = Support::select('sender_id')->groupBy('sender_id')->where('receiver_id', 1)->where('status', 'Unread')->get();
        $unreadSupportUsers = User::whereIn('id', $unreadSupportUserIds)->where('user_type', 'Frontend')->where('status', 'Active')->get();
        return view('backend.support.index', compact('users', 'unreadSupportUsers'));
    }

    public function getUserSupports($userId)
    {
        $user = User::find($userId);
        $messages = Support::where('sender_id', $userId)->orWhere('receiver_id', $userId)->orderBy('created_at', 'asc')->get();

        // Update the status of the messages to read
        $messages->where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
            $message->status = 'Read';
            $message->save();
        });

        // Format the response for the front-end
        $formattedMessages = $messages->map(function ($message) use ($user) {
            return [
                'message' => $message->message,
                'photo' => $message->photo,
                'status' => $message->status,
                'created_at' => $message->created_at->diffForHumans(),
                'profile_photo' => $message->sender_id == auth()->id() ? asset('uploads/profile_photo/' . auth()->user()->profile_photo) : asset('uploads/profile_photo/' . $user->profile_photo),
                'sender_type' => $message->sender_id == auth()->id() ? 'me' : 'friend',
            ];
        });

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'profile_photo' => asset('uploads/profile_photo/' . $user->profile_photo),
            'last_active' => Carbon::parse($user->last_login_at)->diffForHumans(),
            'active_status' => Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline',
            'messages' => $formattedMessages
        ]);
    }

    public function getSearchSupportUser(Request $request)
    {
        $tab = $request->get('tab'); // Get the active tab
        $searchUserId = $request->input('searchUserId', null);
        $receiverId = 1; // Replace with the actual logged-in user's ID or dynamic value if applicable

        $supportUsers = []; // Default empty array for response

        if ($tab === 'unread-supports-users-tab') {
            // Query for unread support users
            $unreadSupportUserIds = Support::select('sender_id')
                ->groupBy('sender_id')
                ->where('receiver_id', $receiverId)
                ->where('status', 'Unread')
                ->pluck('sender_id');

            $query = User::whereIn('id', $unreadSupportUserIds)
                ->where('user_type', 'Frontend')
                ->where('status', 'Active');

            if (!empty($searchUserId)) {
                $query->where('id', $searchUserId);
            }

            $supportUsers = $query->get();
        } elseif ($tab === 'all-users-tab') {
            // Query for all users
            $query = User::where('user_type', 'Frontend')
                ->where('status', 'Active');

            if (!empty($searchUserId)) {
                $query->where('id', $searchUserId);
            }

            $supportUsers = $query->get();
        } else {
            return response()->json(['error' => 'Invalid tab selected'], 400);
        }

        // Process and prepare response
        $response = $supportUsers->map(function ($user) {
            $latestSupport = Support::where('sender_id', $user->id)->latest()->first();
            $message = 'No message';
            if ($latestSupport) {
                if ($latestSupport->message) {
                    $message = strlen($latestSupport->message) > 50
                        ? substr($latestSupport->message, 0, 50) . '...'
                        : $latestSupport->message;
                } elseif ($latestSupport->photo) {
                    $message = '<strong><i data-feather="image" class="icon-sm text-primary"></i> Image</strong>';
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'status' => $user->status,
                'profile_photo' => $user->profile_photo,
                'active_status' => Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline',
                'message' => $message,
                'send_at' => $latestSupport ? $latestSupport->created_at->diffForHumans() : 'No message',
                'support_count' => Support::where('sender_id', $user->id)->where('status', 'Unread')->count(),
            ];
        });

        return response()->json(['supportUsers' => $response, 'tab' => $tab]);
    }

    public function supportSendMessageReply(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->message && !$request->file('photo')) {
                $validator->errors()->add('validator_alert', 'Either a message or a photo is required.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id() . "-support_photo_" . date('YmdHis') . "." . $request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/") . $photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
                $message->status = 'Read';
                $message->save();
            });

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => [
                    'message' => $support->message,
                    'photo' => $support->photo,
                    'sender_id' => $support->sender_id,
                    'created_at' => Carbon::parse($support->created_at)->diffForHumans(),
                ],
            ]);
        }
    }
}
