<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class CustomVerifyCsrfToken extends Middleware
{
    /**
     * Override kalau token CSRF tidak valid.
     */
    protected function invalidToken($request)
    {
        return redirect()->route('login')
            ->withErrors(['session_expired' => 'Sesi Anda sudah habis, silakan login kembali.']);
    }
}
