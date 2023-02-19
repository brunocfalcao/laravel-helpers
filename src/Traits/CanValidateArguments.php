<?php

namespace Brunocfalcao\Helpers\Traits;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Trait used to enhance Commands, so the arguments can be validated.
 */
trait CanValidateArguments
{
    /**
     * Console prompt, but enpowered with rules.
     *
     *
     * @param  string  $question
     * @param  string  $rules
     * @return mixed
     */
    protected function askWithRules(string $question, string|array|Rule $rules)
    {
        $exit = false;
        $answer = null;

        while (! $exit) {
            $answer = $this->ask($question);
            $validator = Validator::make(
                [$question => $answer],
                [$question => $rules]
            );

            if ($validator->fails()) {
                $this->error($validator->errors()->first());
                $exit = false;
            } else {
                $exit = true;
            }
        }

        return $answer;
    }

    /**
     * Validates the command console parameters (arguments or options).
     * Just call the method inside the command class, and pass the rules and
     * if you want to include/ignore null value parameters.
     *
     * @param  array  $rules  Rules array
     * @param  bool  $includeNulls  Should we remove the null values?
     * @return bool Result of the validation. Also triggers a console
     *              error() with the validation error message
     */
    protected function validate(array $rules, bool $includeNulls = true)
    {
        $parameters = array_merge($this->options(), $this->arguments());

        //Remove nulls?
        if (! $includeNulls) {
            $parameters = collect($parameters)->reject(function ($value, $key) {
                return is_null($value);
            })->toArray();
        }

        $validator = Validator::make(
            $parameters,
            $rules
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return false;
        }

        return true;
    }
}
