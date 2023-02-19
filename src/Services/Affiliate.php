<?php

namespace Brunocfalcao\Tracer\Services;

use Brunocfalcao\Cerebrus\ConcernsSessionPersistence;
use Eduka\Cube\Models\Affiliate as AffiliateModel;

class Affiliate
{
    public static function __callStatic($method, $args)
    {
        return AffiliateService::new()->{$method}(...$args);
    }
}

class AffiliateService
{
    use ConcernsSessionPersistence;

    private $affiliate;

    public function __construct()
    {
        $this->withPrefix('tracer::affiliate')
             ->getOr(function () {
                 return $this->refresh();
             });
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }

    /**
     * Connects an affiliate from a possible referrer in session, and
     * returns the possible Eloquent Affiliate instance.
     *
     * @return Referrer|null
     */
    protected function refresh()
    {
        /**
         * This is NOT a referrer object, it is only data referred to
         * the referrer utm_source and domain.
         * An stdClass object with 2 attributes:
         * $referrer->source
         * $referrer->value.
         */
        $referrer = Referrer::get();

        /**
         * If we have a referrer, then let's try to match a possible
         * affiliate in the database, taken in account the product type.
         * If the affiliate is fetched, then it is placed into
         * session, so the method get() can retrieve it.
         */
        $courseId = course()?->id;

        if ($referrer) {
            $affiliate = AffiliateModel::active()
              ->when($courseId, function ($query) use ($courseId) {
                  $query->where('course_id', $courseId);
              })
              ->where('type', 'referrer')
              ->where($referrer->source, $referrer->value)
              ->first();

            if ($affiliate) {
                return $affiliate;
            }
        }

        return null;
    }

    /**
     * Returns a possible collection of fixed affiliates.
     *
     * @return Collection|null
     */
    public function fixed()
    {
        return AffiliateModel::active()
                         ->where('course_id', course()->id)
                         ->where('type', 'fixed')
                         ->get();
    }

    public function fromReferrer()
    {
        return $this->session();
    }
}
