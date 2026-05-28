<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialExceptions  extends CustomDomainException
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
       public function __construct(string $message){
           parent::__construct($message);
       }

       public function statusCode(): int
       {
           return 301;
       }
}
