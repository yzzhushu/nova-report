<?php

namespace Jshxl\Report\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'jshxl:publish')]
class PublishReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jshxl:publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of the jshxl report resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag'   => 'jshxl-report-lang',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag'   => 'jshxl-report-config',
            '--force' => $this->option('force'),
        ]);
    }
}
