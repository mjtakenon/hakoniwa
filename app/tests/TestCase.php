<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use CreatesApplication;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
