<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;

class PolicyListCommand extends Command
{
    protected $signature = 'policy:list';
    protected $description = 'List all registered Eloquent model policies';

    public function handle()
    {
        $policies = Gate::policies();

        $headers = ['Eloquent Model (full qualified namespace)', 'Policy (full qualified namespace)'];

        $data = [];

        foreach ($policies as $model => $policy) {
            $data[] = [$model, $policy];
        }

        $this->table($headers, $data);
    }
}
