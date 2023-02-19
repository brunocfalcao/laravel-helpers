<?php

namespace Brunocfalcao\Tracer\Models;

use Eduka\Abstracts\EdukaModel;

/**
 * A visit ip of any source.
 */
class IpAddress extends EdukaModel
{
    protected $casts = [
        'is_blacklisted' => 'boolean',
        'is_throttled' => 'boolean',
    ];
}
