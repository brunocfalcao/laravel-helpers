<?php

use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

if (! function_exists('log_queries')) {
    function log_queries()
    {
        // Listen to database queries
        DB::listen(function ($query) {
            // Combine the query with its bindings
            $fullQuery = vsprintf(str_replace(['%', '?'], ['%%', "'%s'"], $query->sql), $query->bindings);

            // Log the full query
            info($fullQuery);
        });
    }

    function stop_logging_queries()
    {
        DB::disableQueryLog();
    }
}

if (! function_exists('run')) {

    /**
     * Runs a shell command in a more natural way.
     * E.g.: "run('php artisan migrate')"
     *
     * @return mixed error message, or output message
     */
    function run(string $shellCommand)
    {
        $migrateFreshProcess = new Process(explode(' ', $shellCommand));
        $migrateFreshProcess->run();

        try {
            if (! $migrateFreshProcess->isSuccessful()) {
                throw new ProcessFailedException($migrateFreshProcess);
            }
        } catch (ProcessFailedException $e) {
            return $e->getMessage();
        }
    }
}

if (! function_exists('str_boolean')) {
    function str_boolean(bool|callable $expression)
    {
        if (is_callable($expression)) {
            return $expression() === true ? 'true' : 'false';
        } else {
            return $expression === true ? 'true' : 'false';
        }
    }
}
