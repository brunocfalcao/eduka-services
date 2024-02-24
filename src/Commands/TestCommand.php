<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Video;
use Illuminate\Support\Facades\DB;

class TestCommand extends EdukaCommand
{
    protected $signature = 'eduka:test';

    protected $description = 'Tests a custom code command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('videos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $video = Video::create([
            'name' => 'Just a simple test',
            'description' => 'Ipsis Lorum',
            'chapter_id' => Chapter::find(3)->id,
            'course_id' => Course::find(2)->id,
            'duration' => 166,
            'is_visible' => true,
            'is_active' => true,
            'is_free' => true,
        ]);

        $this->info('All good.');
    }
}
