<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PintCommand extends Command
{
    protected $signature = 'pint';

    protected $description = 'Run pint';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $process = new Process(['./vendor/bin/pint']);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error($process->getErrorOutput());

            return;
        }

        $this->info($process->getOutput());
    }
}
