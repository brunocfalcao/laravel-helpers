<?php

namespace Brunocfalcao\LaravelHelpers\Traits;

use Illuminate\Support\Str;

trait HasUuids
{
    public function upsertUuid($model, string $attribute = 'uuid')
    {
        // Check if the attribute is dirty or blank
        if ($model->isDirty($attribute) || empty($model->$attribute)) {
            // Convert the value to kebab case
            $canonicalValue = (string) Str::uuid();

            // Set or update the model's attribute
            $model->$attribute = $canonicalValue;
        }
    }
}
