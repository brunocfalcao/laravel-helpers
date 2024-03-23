<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForModels;

use Illuminate\Support\Str;

trait HasUuids
{
    public function upsertUuid(string $attribute = 'uuid')
    {
        // Check if the attribute is dirty or blank
        if ($this->isDirty($attribute) || empty($this->$attribute)) {
            // Convert the value to kebab case
            $uuid = (string) Str::uuid();

            // Set or update the model's attribute
            $this->$attribute = $uuid;
        }
    }
}
