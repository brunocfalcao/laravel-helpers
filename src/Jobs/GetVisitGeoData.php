<?php

namespace Brunocfalcao\Tracer\Jobs;

use Brunocfalcao\Tracer\Models\Visit;
use Eduka\Services\Support\ApplicationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetVisitGeoData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $hash;

    public $ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $hash, string $ip)
    {
        $this->hash = $hash;
        $this->ip = $ip == '127.0.0.1' ?
                           '188.62.12.60' : // Just to get a swiss public ip.
                           $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Make the API call with a specific number of fields.
        $response = Http::get('http://ip-api.com/json/' . $this->ip . '?fields=12108287')
                        ->json();

        if ($response['status'] == 'success') {
            Visit::where('hash', $this->hash)
                 ->get()
                 ->each
                 ->updateGeoData($response);
        } else {
            ApplicationLog::properties($response)
                          ->group('error-system')
                          ->log('Visit geocode error');
        }
    }
}
