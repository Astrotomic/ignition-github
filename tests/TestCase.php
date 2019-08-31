<?php

namespace Astrotomic\IgnitionGithubTab\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Astrotomic\IgnitionGithubTab\TabServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TabServiceProvider::class,
        ];
    }
}
