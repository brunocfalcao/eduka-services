<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Chapter;
use Illuminate\Support\Facades\DB;

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
        /*
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('videos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        */

        if ($this->option('create')) {
            $chapter = Chapter::create([
                'name' => 'Deep diving into the Resources',
                'description' => "The Resource logic is the heart of Laravel Nova. Let's dive into what powerful features you can take from it, and how easy is to create your CRUD features to your Eloquent models",
                'course_id' => 1,
            ]);

            $this->info('Chapter created with id '.$chapter->id);

            return;
        }

        $chapter = Chapter::where('name', 'Deep diving into the Resources')->latest()->first();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $chapter->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('All good.');
    }
}
