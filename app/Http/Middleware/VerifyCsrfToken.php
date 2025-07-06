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
        'stripe/webhook',
        'abacatepay/webhook',
        'chat/pix',
        'chat/pix/*',
        'chat/stripe',
        'chat/webhook/*',
        'api/vehicle/lookup',
    ];
}
