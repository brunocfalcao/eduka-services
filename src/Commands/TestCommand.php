<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Episode;
use Illuminate\Support\Str;

class TestCommand extends EdukaCommand
{
    protected $signature = 'eduka:test {--create}';

    protected $description = 'Tests a custom code command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $episode = Episode::whereNotNull('vimeo_uri')->first();

        $episode->name = 'Roger that '.Str::Random(10);

        $episode->save();

        $this->info('All good.');
    }
}
