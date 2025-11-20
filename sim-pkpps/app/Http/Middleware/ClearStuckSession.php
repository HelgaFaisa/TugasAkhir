<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClearStuckSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Auto-clear stuck session untuk guest user
        if (!Auth::check() && $request->session()->has('_token')) {
            $lastActivity = $request->session()->get('last_activity', 0);
            $now = time();
            
            // Jika session idle lebih dari 5 menit, flush
            if (($now - $lastActivity) > 300) {
                $request->session()->flush();
                $request->session()->regenerate();
            }
        }
        
        // Update last activity timestamp
        $request->session()->put('last_activity', time());
        
        return $next($request);
    }
}