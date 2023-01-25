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

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/mrw.php', 'mrw');
        $this->app->bind('migrate:rollback:wizard', MigrationRollbackWizard::class);


        $this->commands([
            'migrate:rollback:wizard',
        ]);
    }
}