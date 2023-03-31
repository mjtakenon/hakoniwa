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

        if (\DB::getConfig()['host'] !== 'db-testing') {
            throw new \Exception('testing環境以外のDBに接続しているため中断します。 host:' . \DB::getConfig()['host']);
        }

        foreach(\DB::select('SHOW TABLES') as $table) {
            if (!$table->Tables_in_hakoniwa === 'migrations') {
                \DB::table($table->Tables_in_hakoniwa)->truncate();
            }
        }

        \Artisan::call('db:seed');

        \DB::beginTransaction();

        return $app;
    }

    public function __destruct()
    {
        \DB::rollBack();
    }

}
