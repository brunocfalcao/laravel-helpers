<?php

namespace Brunocfalcao\Tracer\Services;

use Brunocfalcao\Cerebrus\Cerebrus;
use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;
use Brunocfalcao\Tracer\Jobs\GetVisitGeoData;
use Brunocfalcao\Tracer\Models\Visit as VisitModel;
use Illuminate\Support\Facades\Auth;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Jenssegers\Agent\Facades\Agent;

class Visit
{
    public static function __callStatic($method, $args)
    {
        return VisitService::new()->{$method}(...$args);
    }
}

class VisitService
{
    use ConcernsSessionPersistence;

    public function __construct()
    {
        $this->withPrefix('tracer::visit')
             ->forceRefreshIf(function () {
                 return true;
             })
             ->getOr(function () {
                 return $this->record();
             });
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }

    /**
     * Records a new visit instance. You can merge your own data directly
     * into the visit data if necessary (e.g. for testing purposes or if you
     * want to override a specific parameter).
     *
     * @param  array  $mergingData
     * @return \Brunocfalcao\Tracer\Models\Visit
     */
    protected function record(array $mergingData = [])
    {
        // Create an unique hashcode.
        $hash = md5(request()->ip() .
            Agent::platform() .
            Agent::device());

        $course = course();
        $campaign = Campaign::get(); // It's the querystring value.
        $referrer = Referrer::get(); // It's a stdclass with 2 properties.
        $affiliate = Affiliate::fromReferrer(); // It's an eloquent model.

        // Verify if the request is a bot request.
        $CrawlerDetect = new CrawlerDetect;

        // Check the user agent of the current visit source.
        $isBot = $CrawlerDetect->isCrawler();

        $data = array_merge([
            'session_id' => (new Cerebrus())->getId(),
            'is_bot' => $isBot,
            'user_id' => Auth::id(),
            'path' => request()->path(),
            'route_name' => optional(request()->route())->getName(),
            'goal_id' => null, // To be implemented.
            'affiliate_id' => optional($affiliate)->id, // To be implemented.
            'campaign' => $campaign,
            'hash' => $hash,
        ], $mergingData);

        if ($referrer) {
            // 2 parameters: utm_source and/or domain.
            $data = array_merge($data, [
                'referrer_' . $referrer->source => $referrer->value,
            ]);
        }

        // Insert visit.
        $visit = VisitModel::create($data);

        // Update georeference data for the current hash (queued job call).
        GetVisitGeoData::dispatch($hash, public_ip());

        return $visit;
    }

    public function get()
    {
        return $this->session();
    }
}
