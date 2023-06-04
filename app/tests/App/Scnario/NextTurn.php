<?php

namespace Tests\App\Scnario;

use Illuminate\Console\Command;
use Tests\TestCase;

class NextTurn extends TestCase
{
    public function test()
    {
        \Artisan::call('db:seed --class=RegisterIslandSeeder --env=testing');
        $result = \Artisan::call('execute:turn --env=testing');
        $this->assertSame(Command::SUCCESS, $result);
    }
}
