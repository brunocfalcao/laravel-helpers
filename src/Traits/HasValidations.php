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
        $validator = Validator::make($this->getAttributes(), array_merge($extraRules, $this->rules));

        if ($validator->fails()) {
            if (request()->headers->has('referer')) {
                throw ValidationException::withMessages($validator->messages()->toArray());
            } else {
                throw new \Exception($validator->errors()->first());
            }
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
