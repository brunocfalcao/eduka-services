<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;

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
        //dd(Course::find(2)->getFirstMediaUrl(conversionName: 'thumbnail'));

        //$thumbnailUrl = Course::find(4)->getFirstMediaUrl('main', 'thumbnail');

        //dd($thumbnailUrl);

        $this->info('All good.');
    }
}
