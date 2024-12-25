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
        $deviceType = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent) ? 'Smartphone' :
            (preg_match('/Windows|Macintosh|Linux/', $userAgent) ? 'Computer' : 'Unknown Device');

        // Detect the browser
        preg_match('/(Chrome|Firefox|Safari|Opera|Edge)\/[0-9.]+/', $userAgent, $browserMatches);
        $browser = $browserMatches[1] ?? 'Unknown Browser';

        // Get the current user's IP
        $userIp = $request->ip();

        // Check if a record with the same user_id and IP exists
        $userDetail = UserDetail::where('user_id', auth()->user()->id)
            ->where('ip', $userIp)
            ->first();

        if ($userDetail) {
            // Update the existing record's updated_at field
            $userDetail->update(['updated_at' => now()]);
        } else {
            // Create a new record with full details
            UserDetail::create([
                'user_id' => auth()->user()->id,
                'ip' => $userIp,
                'device' => $deviceType,
                'browser' => $browser,
                'updated_at' => now(),
            ]);
        }

        return $next($request);
    }
}
