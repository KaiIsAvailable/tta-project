<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application, or 'null' to trust all proxies.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // or specific IPs

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = [
        Request::HEADER_FORWARDED,
        Request::HEADER_X_FORWARDED_FOR,
        Request::HEADER_X_FORWARDED_HOST,
        Request::HEADER_X_FORWARDED_PROTO,
        Request::HEADER_X_FORWARDED_PORT,
    ]; 
}
