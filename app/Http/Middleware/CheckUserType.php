<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevice;
use Stevebauman\Location\Facades\Location;

class CheckUserType
{
    public function handle(Request $request, Closure $next, $user_type): Response
    {
        if ($user_type === 'Backend' && !Auth::check()) {
            return redirect()->route('backend.login')->with('error', 'You need to login to access the backend.');
        }

        if ($request->user()->user_type !== $user_type) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        $userAgent = $request->header('User-Agent');
        $userIp = $request->ip();

        // Detect device type and name
        $deviceType = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent) ? 'Smartphone' :
            (preg_match('/Windows|Macintosh|Linux/', $userAgent) ? 'Computer' : 'Unknown Device');

        $deviceName = 'Unknown Device';
        if ($deviceType === 'Smartphone') {
            preg_match('/(iPhone|iPad|Samsung|Huawei|Xiaomi|OnePlus|Google)/i', $userAgent, $deviceMatches);
            $deviceName = $deviceMatches[1] ?? 'Unknown Smartphone';
        } elseif ($deviceType === 'Computer') {
            preg_match('/(HP|Dell|Lenovo|Asus|Acer|Walton|Microsoft|Apple|Samsung)/i', $userAgent, $deviceMatches);
            $deviceName = $deviceMatches[1] ?? 'Unknown Computer';
        }

        // Detect browser
        preg_match('/(Chrome|Firefox|Safari|Opera|Edge)\/[0-9.]+/', $userAgent, $browserMatches);
        $browser = $browserMatches[1] ?? 'Unknown Browser';

        // Use cached location details
        $cacheKey = "location_{$userIp}";
        $location = cache()->remember($cacheKey, now()->addHours(24), function () use ($userIp) {
            return Location::get($userIp);
        });

        $country = $location->countryName ?? 'Unknown Country';
        $region = $location->regionName ?? 'Unknown Region';
        $city = $location->cityName ?? 'Unknown City';
        $latitude = $location->latitude ?? null;
        $longitude = $location->longitude ?? null;

        // Efficiently update or create user Devices
        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'ip' => $userIp],
            [
                'device_type' => $deviceType,
                'device_name' => $deviceName,
                'browser' => $browser,
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        )->touch(); // Update only the `updated_at` timestamp.

        return $next($request);
    }

}
