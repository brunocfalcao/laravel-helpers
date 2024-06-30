<?php

namespace Brunocfalcao\LaravelHelpers\Traits\ForServiceProviders;

use Illuminate\Support\Facades\Gate;

trait HasAutoLoaders
{
    protected function autoloadPolicies(string $providerDir)
    {
        $callingClass = substr(get_called_class(), 0, strrpos(get_called_class(), '\\'));

        $modelPaths = glob($providerDir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "{$callingClass}\\Models\\{$model}";
            $policyClass = "{$callingClass}\\Policies\\{$model}Policy";

            try {
                if (class_exists($modelClass) && class_exists($policyClass)) {
                    $modelClassObject = new $modelClass;
                    $policyClassObject = new $policyClass;

                    Gate::policy(get_class($modelClassObject), get_class($policyClassObject));
                }
            } catch (\Exception $ex) {
                //
            }
        }
    }

    protected function autoloadGlobalScopes(string $providerDir)
    {
        $callingClass = substr(get_called_class(), 0, strrpos(get_called_class(), '\\'));

        $modelPaths = glob($providerDir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "{$callingClass}\\Models\\{$model}";
            $scopeClass = "{$callingClass}\\Scopes\\{$model}Scope";

            try {
                if (class_exists($modelClass) && class_exists($scopeClass)) {
                    $modelClass::addGlobalScope(new $scopeClass);
                }
            } catch (\Exception $ex) {
                //
            }
        }
    }

    protected function autoloadObservers(string $providerDir)
    {
        $callingClass = substr(get_called_class(), 0, strrpos(get_called_class(), '\\'));

        $modelPaths = glob($providerDir.'/Models/*.php');
        $modelClasses = array_map(function ($path) {
            return basename($path, '.php');
        }, $modelPaths);

        foreach ($modelClasses as $model) {
            $modelClass = "{$callingClass}\\Models\\{$model}";
            $observerClass = "{$callingClass}\\Observers\\{$model}Observer";

            try {
                if (class_exists($modelClass) && class_exists($observerClass)) {
                    $modelClass::observe($observerClass);
                }
            } catch (\Exception $ex) {
                //
            }
        }
    }
}
