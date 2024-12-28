<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponderTrait;

class CheckForUserStatus
{
    use ApiResponderTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is logged in
        if (auth()->check()) {

            // Check if the user is blocked
            if (auth()->user()->blocked == true) {

                // Log the user out
                auth()->logout();

                // Return failure
                return $this->forbiddenAccess('Your account is blocked, please contact support');
            }
        }

        return $next($request);
    }
}
