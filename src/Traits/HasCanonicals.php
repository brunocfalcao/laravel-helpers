<?php

namespace Brunocfalcao\LaravelHelpers\Traits;

use Illuminate\Support\Str;

trait HasCanonicals
{
    public function upsertCanonical($model, string $value, string $attribute = 'canonical')
    {
        // Check if the attribute is dirty or blank
        if ($model->isDirty($attribute) || empty($model->$attribute)) {
            // Convert the value to kebab case
            $canonicalValue = Str::slug($value, '-');

            // Set or update the model's attribute
            $model->$attribute = $canonicalValue;
        }
    }
}
