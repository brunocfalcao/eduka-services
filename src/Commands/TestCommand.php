<?php

namespace Eduka\Services\Commands;

use Brunocfalcao\Tokenizer\Models\Token;
use Eduka\Abstracts\Classes\EdukaCommand;

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
        $token = Token::createToken()->token;

        Token::burn($token);
    }
}
