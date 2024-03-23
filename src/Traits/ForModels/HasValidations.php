<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForModels;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasValidations
{
    /**
     * Default method, to be overriden by the used class.
     *
     * @return array
     */
    private function _getRules()
    {
        $rules = [];

        if ($this->rules) {
            $rules = array_merge($rules, $this->rules);
        }

        if (method_exists($this, 'getRules')) {
            $rules = array_merge($rules, $this->getRules());
        }

        return $rules;
    }

    /**
     * Validates the eloquent model.
     *
     * @param  array  $extraRules  In case want to add/overwrite the model rules
     * @return mixed
     */
    public function validate(array $extraRules = [])
    {
        $validator = Validator::make($this->getAttributes(), array_merge($extraRules, $this->_getRules()));

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
        return data_get(collect($this->_getRules()), $name);
    }
}
