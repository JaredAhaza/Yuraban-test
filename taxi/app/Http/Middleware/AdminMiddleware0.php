<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //if (Auth::check()) {
          //  \Log::info('User is authenticated', ['user' => Auth::user()]);
            
            //if (Auth::user()->isAdmin()) {
                return $next($request);
           // }
       // }

        //\Log::warning('User is not admin or not authenticated', ['user' => Auth::user()]);
        //return redirect()->route('home'); // Change this to your desired route
    }
}
