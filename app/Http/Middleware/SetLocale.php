<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Import App facade
use Illuminate\Support\Facades\Session; // Import Session facade
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the application locale based on the session value.
        // If no locale is in session, it defaults to the value in config/app.php ('es' in your case).
        App::setLocale(Session::get('locale', config('app.locale')));

        return $next($request);
    }
}