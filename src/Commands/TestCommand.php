<?php

namespace Eduka\Services\Commands;

use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Variant;
use Illuminate\Support\Str;

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
        Variant::find(2)->update(['description' => Str::random(50)]);

        dd(Variant::find(2)->lemon_squeezy_data);

        $this->info('All good.');
    }
}
