<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\User;

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
        // Let's delete my test user so we can use it for testing purposes.
        User::where('email', env('MN_OR_TEST_EMAIL'))->first()
            ->videosThatWereCompleted()
            ->detach();

        User::where('email', env('MN_OR_TEST_EMAIL'))->forceDelete();
    }
}
