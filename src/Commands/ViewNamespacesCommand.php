<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\View\Factory as ViewFactory;
use Symfony\Component\Console\Helper\Table;

class ViewNamespacesCommand extends Command
{
    protected $signature = 'view:namespaces';

    protected $description = 'List all the loaded view namespaces';

    public function handle(ViewFactory $viewFactory)
    {
        $viewNamespaces = $viewFactory->getFinder()->getHints();

        $data = [];
        foreach ($viewNamespaces as $namespace => $paths) {
            $paths = $paths ?: [base_path('resources/views')]; // Set default path if no paths are found
            foreach ($paths as $path) {
                $data[] = [$namespace ?: 'Default', $path];
            }
        }

        $headers = ['Namespace', 'Path'];

        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($data);
        $table->render();
    }
}
