<?php

namespace Tests\App\Services\Hakoniwa;

use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    public function testGetAllPlans()
    {
        print_r(\PlanService::getAllPlans());
        $this->assertTrue(true);
    }
}
