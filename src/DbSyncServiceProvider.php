<?php

namespace Lupka\LaravelDbSync;

use Illuminate\Support\ServiceProvider;

class DbSyncServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDependencies();
        $this->registerDbSyncCommand();
    }


    /**
     * Register dependencies
     */
    private function registerDependencies()
    {
        $this->app->register('Collective\Remote\RemoteServiceProvider');
    }

    /**
     * Register the db:sync function.
     */
    private function registerDbSyncCommand()
    {
        $this->app->singleton('command.lupka.db-sync', function ($app) {
            return $app['Lupka\LaravelDbSync\Commands\DbSyncCommand'];
        });

        $this->commands('command.lupka.db-sync');
    }
}
