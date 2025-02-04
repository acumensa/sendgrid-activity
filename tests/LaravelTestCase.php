<?php

namespace StephaneCoinon\SendGridActivity\Tests;

use StephaneCoinon\SendGridActivity\Integrations\Laravel\SendGridApiServiceProvider;

class LaravelTestCase extends \Orchestra\Testbench\TestCase
{
    use Support\Traits\LoadsEnv;

    public function setUp(): void
    {
        // $this->loadEnv();

        parent::setUp();

        $this->withFactories(__DIR__.'/Support/factories.php');
    }

    protected function getPackageProviders($app)
    {
        return [SendGridApiServiceProvider::class];
    }
}
