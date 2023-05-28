<?php

namespace Brunocfalcao\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HelpersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMacros();
        $this->registerBladeDirectives();
    }

    public function register(): void
    {
        //
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
