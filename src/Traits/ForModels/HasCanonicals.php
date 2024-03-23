<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForModels;

use Illuminate\Support\Str;

trait HasCanonicals
{
    /**
     * Autogenerates a canonical.
     *
     * @param  string  $value  The column name to get the value for generation
     * @param  string  $attribute  The column attribute where the value will be saved
     * @return void
     */
    public function upsertCanonical(string $value = 'name', string $attribute = 'canonical')
    {
        if ($this->isDirty($attribute) || blank($this->$attribute)) {
            $canonical = Str::slug($this->$value, '-').'-'.strtolower(Str::random(4));
            $this->$attribute = $canonical;
        }
    }
}
