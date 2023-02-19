<?php

namespace Brunocfalcao\Tracer\Models;

use Eduka\Abstracts\EdukaModel;
use Eduka\Cube\Models\Affiliate;
use Eduka\Cube\Models\ApplicationLog;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\User;
use Laravel\Paddle\Billable;

class Visit extends EdukaModel
{
    use Billable;

    // Relationship verified.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship verified.
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relationship verified.
    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    // Relationship verified.
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    // Relationship verified.
    public function logs()
    {
        return $this->morphMany(ApplicationLog::class, 'causable');
    }

    /**
     * Adds GeoData from the retrieved visit source.
     *
     * @param  array  $data
     * @return void
     */
    public function updateGeoData(array $data)
    {
        $this->continent = $data['continent'];
        $this->continentCode = $data['continentCode'];
        $this->country = $data['country'];
        $this->countryCode = $data['countryCode'];
        $this->region = $data['region'];
        $this->regionName = $data['regionName'];
        $this->city = $data['city'];
        $this->district = $data['district'];
        $this->zip = $data['zip'];
        $this->latitude = $data['lat'];
        $this->longitude = $data['lon'];
        $this->timezone = $data['timezone'];
        $this->currency = $data['currency'];

        $this->update();
    }
}
