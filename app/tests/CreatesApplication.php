<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        if (!\App::environment('testing')) {
            throw new \Exception('testing環境以外のDBに接続しているため中断します。 environment:' . \App::environment());
        }

        return $app;
    }
}
