<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $user_type): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('backend.login');
        }

        // Check if the user type is not the same as the user type in the request
        if ($request->user()->user_type !== $user_type) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
