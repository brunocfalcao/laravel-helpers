<?php

namespace Brunocfalcao\LaravelHelpers;

use Brunocfalcao\LaravelHelpers\Commands\ChangeUserPasswordCommand;
use Brunocfalcao\LaravelHelpers\Commands\MakeUserCommand;
use Brunocfalcao\LaravelHelpers\Commands\PintCommand;
use Brunocfalcao\LaravelHelpers\Commands\PolicyListCommand;
use Brunocfalcao\LaravelHelpers\Commands\ViewNamespacesCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LaravelHelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacros();
        $this->registerBladeDirectives();
        $this->registerCommands();
    }

    public function register(): void
    {
        //
    }

    protected function registerCommands()
    {
        $this->commands([
            ChangeUserPasswordCommand::class,
            PolicyListCommand::class,
            ViewNamespacesCommand::class,
            MakeUserCommand::class,
            PintCommand::class,
        ]);
    }

    protected function registerMacros(): void
    {
        // Include all files from the Macros folder.
        Collection::make(glob(__DIR__.'/Macros/*.php'))
                  ->mapWithKeys(function ($path) {
                      return [$path => pathinfo($path, PATHINFO_FILENAME)];
                  })
                  ->each(function ($macro, $path) {
                      require_once $path;
                  });
    }

    protected function registerBladeDirectives(): void
    {
        Blade::directive('info', function ($expression) {
            return "<?php app('log')->info({$expression}); ?>";
        });
    }
}
