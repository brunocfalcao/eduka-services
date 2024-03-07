<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Video;
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

        $video = Video::whereNotNull('vimeo_uri')->first();

        $video->name = 'Roger that '.Str::Random(10);

        $video->save();

        $this->info('All good.');
    }
}
