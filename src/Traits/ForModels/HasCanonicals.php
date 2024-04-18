<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForModels;

use Illuminate\Support\Str;

trait HasCanonicals
{
    /**
     * Autogenerates a canonical.
     *
     * @param  string  $column  The column name to get the value for generation
     * @param  string  $attribute  The attribute where the value will be saved
     * @param  bool  $randomHash  If true, adds 4 random letters to the end
     * @return void
     */
    public function upsertCanonical(string $column = 'name', string $attribute = 'canonical', bool $randomHash = false)
    {
        $update = false || blank($this->$attribute);

        if ($update) {
            $canonical = Str::slug($this->$column, '-');

            if ($randomHash) {
                $canonical .= '-'.strtolower(Str::random(4));
            }

            $this->$attribute = $canonical;
        }
    }
}
