<?php

namespace Brunocfalcao\LaravelHelpers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasValidations
{
    /**
     * Validates the eloquent model.
     *
     * @param  array  $extraRules  In case want to add/overwrite the model rules
     * @return mixed
     */
    public function validate(array $extraRules = [])
    {
        $validator = Validator::make($this->getAttributes(), array_merge($this->rules, $extraRules));

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->messages()->toArray());
        }

        return true;
    }

    /**
     * Obtains a single validation rule (mostly for Nova fields).
     *
     * @return null|string
     */
    public function rule(string $name)
    {
        return data_get(collect($this->rules), $name);
    }
}
