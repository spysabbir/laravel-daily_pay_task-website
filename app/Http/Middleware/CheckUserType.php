<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDetail;
use Carbon\Carbon;

class CheckUserType
{
    public function handle(Request $request, Closure $next, $user_type): Response
    {
        // Check if the user type is Backend and the user is not logged in
        if ($user_type === 'Backend' && !Auth::check()) {
            return redirect()->route('backend.login')->with('error', 'You need to login to access the backend.');
        }

        // Check if the user type is not the same as the user type in the request
        if ($request->user()->user_type !== $user_type) {
            abort(403, 'Unauthorized action.');
        }

        // Update the last login at timestamp
        Auth::user()->update(['last_login_at' => now()]);

        // Get user-agent from the request
        $userAgent = $request->header('User-Agent');
        // Detect the device type
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'Smartphone';
        } elseif (preg_match('/Windows|Macintosh|Linux/', $userAgent)) {
            $deviceType = 'Computer';
        } else {
            $deviceType = 'Unknown Device';
        }
        // Detect the browser
        preg_match('/(Chrome|Firefox|Safari|Opera|Edge)\/[0-9.]+/', $userAgent, $browserMatches);
        $browser = $browserMatches[1] ?? 'Unknown Browser';
        // Update or create user details
        UserDetail::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'ip' => $request->ip(),
                'device' => $deviceType,
                'browser' => $browser,
                'updated_at' => Carbon::now(),
            ]
        );

        return $next($request);
    }
}
