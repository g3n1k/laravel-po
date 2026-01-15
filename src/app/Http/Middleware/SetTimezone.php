<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set default timezone to Asia/Jakarta
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        // Optionally, you can set timezone based on user preference if available
        if (auth()->check() && auth()->user()->timezone) {
            date_default_timezone_set(auth()->user()->timezone);
        }

        return $next($request);
    }
}
