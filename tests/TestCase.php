<?php

namespace Igniter\Pages\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \Igniter\Pages\Extension::class,
        ];
    }
}
