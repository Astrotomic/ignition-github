<?php

namespace Astrotomic\IgnitionGithubTab;

use Facade\Ignition\Ignition;
use Illuminate\Support\ServiceProvider;

class TabServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Ignition::tab(app(Tab::class));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
