<?php

namespace Brunocfalcao\Tracer\Middleware;

use Closure;
use Illuminate\Http\Request;

class GoalsTracing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        foreach (config('tracer.goals') as $goal) {
            (new $goal)();
        }

        return $next($request);
    }
}
