<?php

namespace Brunocfalcao\Tracer\Models;

use Eduka\Abstracts\EdukaModel;
use Eduka\Cube\Models\ApplicationLog;

class Goal extends EdukaModel
{
    protected $casts = [
        'attributes' => 'array',
    ];

    // Relationship verified.
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    // Relationship verified.
    public function logs()
    {
        return $this->morphMany(ApplicationLog::class, 'causable');
    }
}
