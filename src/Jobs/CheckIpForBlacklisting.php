<?php

namespace Brunocfalcao\Tracer\Jobs;

use Brunocfalcao\Tracer\Models\IpAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckIpForBlacklisting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dnsLookups = config('tracer.dns.blacklist_servers');

        $reverseIp = implode('.', array_reverse(explode('.', $this->ip)));

        foreach ($dnsLookups as $host) {
            if (checkdnsrr($reverseIp . '.' . $host . '.', 'A')) {
                IpAddress::where('ip_address', $this->ip)
                         ->update(['is_blacklisted' => true]);
            }
        }
    }
}
