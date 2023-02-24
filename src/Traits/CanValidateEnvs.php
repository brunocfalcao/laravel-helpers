<?php

namespace Brunocfalcao\Helpers\Traits;

use Illuminate\Support\Collection;

/**
 * Trait used to allow classes to validate ENV variables.
 */
trait CanValidateEnvs
{
    private $envValidationMsg;

    private $envValidated;

    /**
     * Validates a collection of environment variables.
     *
     * @param  Collection  $envVars  Array of env keys and values. Like:
     *                               ['Env-Key' => null, ...] => The env key must exist and must not be null.
     *                               ['Env-Key' => value, ...]=> The env key must be equal to the value.
     *                               ['Env-Key' => [null, value, ...], ...] => The env key must contain one
     *                               of the values passed on the value array.
     * @return bool The result of the validation. Then check
     *              $this->getEnvErrorMessage().
     */
    public function validateEnvVars(Collection $envVars)
    {
        [$this->envValidationMsg, $this->envValidated] = [null, true];

        $envVars->each(function ($item, $key) {
            // Array values to be tested.
            if (is_array($item)) {
                $contains = false;
                // Validate each array value into the possible values.
                collect($item)->each(function ($value) use ($key, &$contains) {
                    // Collection value is null?
                    if (is_null($value) && is_null(env($key))) {
                        $this->envValidationMsg = '.env '.$key.' cannot be null / must exist';
                        $this->envValidated = false;

                    // Collection value equals to env value?
                    } elseif ($value == env($key)) {
                        $this->envValidationMsg = null;
                        $this->envValidated = true;
                        $contains = true;
                    }
                });

                // Process result.
                if (! $contains) {
                    $this->envValidationMsg = '.env '.$key.' does not contain a possible value: "'.collect($item)->join('", "').'"';
                    $this->envValidated = false;
                }

            // Non-array key value to be tested.
            } else {
                // Is it null?
                if (is_null($item) && is_null(env($key))) {
                    $this->envValidationMsg = '.env '.$key.' cannot be null / must exist';
                    $this->envValidated = false;
                }

                // Same value as env value?
                if (! is_null($item) && env($key) != $item) {
                    $this->envValidationMsg = '.env '.$key.' must be equal to '.$item;
                    $this->envValidated = false;
                }
            }
        });

        return $this->envValidated;
    }

    public function envValidationMsg()
    {
        return $this->envValidationMsg;
    }
}
