<?php

use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Dumps info() but with any number of arguments.
 */
if (! function_exists('info_multiple')) {
    function info_multiple(...$messages)
    {
        foreach ($messages as $message) {
            info($message);
        }
    }
}

/**
 * Returns all env variables except comments.
 * Empty .env values from existing keys, will be returned as null.
 */
if (! function_exists('get_env_variables')) {
    function get_env_variables(): array
    {
        $path = app()->environmentFilePath();
        $contents = file_get_contents($path);
        $lines = explode("\n", $contents);
        $env = [];

        foreach ($lines as $line) {
            // Skip comments and empty lines
            if (empty($line) || str_starts_with(trim($line), '#')) {
                continue;
            }

            // Split string on the first occurrence of "="
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                [$key, $value] = $parts;
                $key = trim($key);
                $value = trim($value);

                // Remove surrounding quotes from value
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                $env[$key] = $value === '' ? null : $value;
            }
        }

        return $env;
    }
}

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
