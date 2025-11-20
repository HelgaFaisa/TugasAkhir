<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // JANGAN tambahkan apa-apa disini, biarkan kosong
    ];
    
    /**
     * Add CSRF token to response headers
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        if ($config['driver'] === 'array') {
            return $response;
        }

        $response->headers->setCookie(
            $this->newCookie($request, $config)
        );

        return $response;
    }
}