<?php

use Illuminate\Console\Command;

Command::macro('paragraph', function ($text, $endlf = true, $startlf = true) {
    if ($startlf) {
        $this->info('');
    }
    $this->info($text);
    if ($endlf) {
        $this->info('');
    }
});
