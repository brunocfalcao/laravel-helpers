<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForModels;

use Brunocfalcao\LaravelHelpers\Classes\CustomEloquentQueryBuilder;

trait HasCustomQueryBuilder
{
    public function newEloquentBuilder($query)
    {
        return new CustomEloquentQueryBuilder($query);
    }
}
