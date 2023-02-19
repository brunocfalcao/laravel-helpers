<?php

namespace Brunocfalcao\Tracer\Services;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;

class Campaign
{
    public static function __callStatic($method, $args)
    {
        return CampaignService::new()->{$method}(...$args);
    }
}

class CampaignService
{
    use ConcernsSessionPersistence;

    public function __construct()
    {
        $this->withPrefix('tracer::campaign')
             ->getOr(function () {
                 return request()->query('cmpg', null);
             });
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }

    public function get()
    {
        return $this->session();
    }
}
