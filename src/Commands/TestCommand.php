<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Subscriber;

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
        Subscriber::create([
            'email' => 'bruno.falcao@live.com',
            'course_id' => 2,
        ]);
    }
}
