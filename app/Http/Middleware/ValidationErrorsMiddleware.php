<?php

namespace App\Http\Middleware;

use Closure;
use TwigBridge\Facade\Twig;

class ValidationErrorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (array_key_exists('errors', $_SESSION)) {
            Twig::addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }
        return $next($request);
    }
}
