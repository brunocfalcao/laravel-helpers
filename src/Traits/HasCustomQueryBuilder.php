<?php

namespace Brunocfalcao\LaravelHelpers\Traits;

use Brunocfalcao\LaravelHelpers\Classes\CustomEloquentQueryBuilder;

trait HasCustomQueryBuilder
{
    public function newEloquentBuilder($query)
    {
        return new CustomEloquentQueryBuilder($query);
    }
}
