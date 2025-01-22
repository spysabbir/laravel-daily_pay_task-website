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
        $user->update(['last_activity_at' => now()]);

        $userAgent = $request->header('User-Agent');
        $userIp = $request->ip();

        $deviceType = 'Unknown Device';
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'Smartphone';
        } elseif (preg_match('/Windows|Macintosh|Linux/', $userAgent)) {
            $deviceType = 'Computer';
        } else {
            $deviceType = 'Unknown Device';
        }

        // Determine Device OS
        $deviceOs = 'Unknown OS';
        if (preg_match('/(Windows|Macintosh|Linux|Android|iPhone|iPad)/', $userAgent, $osMatches)) {
            $deviceOs = $osMatches[1];
        } elseif (preg_match('/(iOS|Android) [0-9.]+/', $userAgent, $versionMatches)) {
            $deviceOs = $versionMatches[1];
        }

        // Determine Browser
        $browser = 'Unknown Browser';
        if (preg_match('/(Chrome|Firefox|Safari|Opera|Edge)\/[0-9.]+/', $userAgent, $browserMatches)) {
            $browser = $browserMatches[1];
        }

        $cacheKey = "location_{$userIp}";
        $location = cache()->remember($cacheKey, now()->addHours(24), function () use ($userIp) {
            return Location::get($userIp);
        });

        $country = $location->countryName ?? 'Unknown Country';
        $region = $location->regionName ?? 'Unknown Region';
        $city = $location->cityName ?? 'Unknown City';
        $latitude = $location->latitude ?? null;
        $longitude = $location->longitude ?? null;

        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'ip_address' => $userIp],
            [
                'device_type' => $deviceType,
                'device_os' => $deviceOs,
                'browser' => $browser,
                'country' => $country,
                'region' => $region,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        )->touch();

        return $next($request);
    }
}
