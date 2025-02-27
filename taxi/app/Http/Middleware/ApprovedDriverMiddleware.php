<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedDriverMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_approved) {
            return $next($request);
        }

        return redirect('/driver/profile')->with('error', 'Your account is not approved yet. Please wait for admin approval.');
    }
}