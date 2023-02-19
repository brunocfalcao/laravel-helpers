<?php

namespace Brunocfalcao\Tracer\Middleware;

use Brunocfalcao\Tracer\Services\Visit;
use Closure;
use Illuminate\Http\Request;

class VisitTracing
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
        //Let's trigger a visit recording by instanciating the class.
        $visit = new Visit();

        return $next($request);
    }
}
