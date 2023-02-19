<?php

namespace Brunocfalcao\Tracer\Services;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;

class Referrer
{
    public static function __callStatic($method, $args)
    {
        return ReferrerService::new()->{$method}(...$args);
    }
}

/**
 * Analyses if there is a referrer that should be taken in context for the
 * current user session. The referrer information can be used for visits
 * analytics, or to connect the referrer utm_source/domain to an
 * affiliate model.
 *
 * REMARK:
 * We cannot connect a referrer to an affiliate WITHOUT having a product id
 * to contextualize the affiliate. Because affiliates are not part
 * of all the products that will exist in the LMS for all the courses.
 */
class ReferrerService
{
    use ConcernsSessionPersistence;

    private $utmSource = null;

    private $domain = null;

    /**
     * This is not an affiliate model, but a stdClass with 2 properties:
     * ->source (if it's 'utm_source' or 'domain', db columns from affiliates)
     * ->value (the value of the referrer value for the respective case).
     *
     * E.g.: Referrer with http header "referer" = 'laravel.com':
     * ->source = 'domain'
     * ->value = 'laravel.com'
     *
     * E.g.: Referrer with utm_source "?utm_source=laravelnews"
     * ->source = 'utm_source'
     * ->value = 'laravelnews'
     *
     * The utm_source querystring has higher priority than the http header.
     *
     * @var stdClass
     */
    private $referrer;

    public function __construct()
    {
        $this->withPrefix('tracer::referrer')
             ->getOr(function () {
                 return $this->compute();
             });
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }

    /**
     * Returns an stdClass with 2 properties (source and value) for the
     * referrer, in case it exists.
     *
     * @return stdClass|null
     */
    public function get()
    {
        return $this->session();
    }

    /**
     * Computes a possible referrer given:
     * Request input "utm_course" or.
     *
     * @return void
     */
    public function compute()
    {
        $result = new \stdClass();

        $this->referrer = null;

        if (request()->input('utm_source') || env('EDUKA_UTM_SOURCE')) {
            $result->source = 'utm_source';
            $result->value = env('EDUKA_UTM_SOURCE') ??
                             request()->input('utm_source');
        } elseif (request()->headers->get('referer') || env('EDUKA_REFERRER')) {
            $result->source = 'domain';
            $result->value = domain(env('EDUKA_REFERRER') ??
                                    request()->headers->get('referer'));
        }

        return $result;
    }
}
