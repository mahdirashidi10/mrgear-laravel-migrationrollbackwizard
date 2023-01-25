<?php

namespace Danny\Migrations\ServiceProviders;

use Danny\Migrations\Console\Commands\MigrationRollbackWizard;
use Illuminate\Support\ServiceProvider;

class MigrationRollbackWizardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/mrw.php' => config_path('mrw.php'),
        ], 'config');
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrationRollbackWizard::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/mrw.php', 'mrw');
    }
}