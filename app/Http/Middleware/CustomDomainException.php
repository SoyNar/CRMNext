<?php

namespace App\Http\Middleware;

use Closure;
use Exception;use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomDomainException extends  Exception
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function __construct($message = ""){
        parent::__construct($message);
    }

    public function statusCode():int
    {
        return 404;
    }
}
