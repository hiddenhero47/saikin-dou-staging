<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponderTrait;

class EnsureTeamMembership
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
        // Check if there is a Team key in the request headers
        if (empty($request->hasHeader('Team'))) {

            // Return failure
            return $this->badRequest('Team, key and value are missing in request header.');
        }

        // Check if user relates to supplied team
        if (!auth()->user()->belongsToTeam($request->header('Team')) ){

            // Return failure
            return $this->forbiddenAccess('You do not belong to the supplied team.');
        }

        return $next($request);
    }
}
