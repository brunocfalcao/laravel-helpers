<?php

namespace Brunocfalcao\LaravelHelpers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Just add it to your observer, and then you can make things like:
 *      $this->validate($model, [
            'name' => 'required',
            'canonical' => 'unique:authorizations|starts_with:questionnaire,client',
            'description' => 'required',
        ]);

        In case the validation rules fail, it will automatically
        throw a controlled validation exception that can be used on Forms
        and also on Nova form fields!
 */
trait CanValidateObserverAttributes
{
    protected function validate(Model $model, array $rules)
    {
        $validator = Validator::make($model->getAttributes(), $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->messages()->toArray());
        }
    }
}
