<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponderTrait;

class AlwaysRespondWithJson
{
    use ApiResponderTrait;

    /**
     * Handle an incoming request.
     * Change the Request headers to accept "application/json".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        if ($request->header('Accept') != 'application/json') {

            // Return failure
            return $this->wrongRequestType('Header must Accept:application/json');
        }

        return $next($request);
    }
}
