<?php

namespace Brunocfalcao\Tracer\Middleware;

use Brunocfalcao\Tracer\Jobs\CheckIpForBlacklisting;
use Brunocfalcao\Tracer\Models\IpAddress;
use Closure;
use Eduka\Abstracts\EdukaException;
use Illuminate\Http\Request;

class IpTracing
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
        $record = IpAddress::where('ip_address', public_ip())->firstOrNew([
            'ip_address' => public_ip(),
        ]);

        $record->increment('hits');
        $record->save();

        // Local environments don't need to check for blacklist of throttling.
        if (app()->environment() == 'local') {
            return $next($request);
        }

        if ($record->is_throttled) {
            throw new EdukaException('Sorry, your IP address is throttled. Please wait until it is released, or if not please contact ' . env('APP_NAME') . ' support');
        }

        /*
        if ($record->is_blacklisted) {
            throw new EdukaException('Sorry, your IP address ('.public_ip().') is blacklisted. Please contact '.env('APP_NAME').' support');
        }
        */

        // Update IP blacklist analysis, if necessary.
        CheckIpForBlacklisting::dispatch(public_ip());

        return $next($request);
    }
}
