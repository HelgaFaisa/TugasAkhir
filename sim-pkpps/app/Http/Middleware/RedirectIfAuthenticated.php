<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $path = $request->path();

        if (str_starts_with($path, 'santri')) {
            // Halaman guest santri → redirect hanya jika guard santri aktif
            if (Auth::guard('santri')->check()) {
                return redirect()->route('santri.dashboard');
            }
        } else {
            // Halaman guest admin → redirect hanya jika guard web aktif
            if (Auth::check()) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}