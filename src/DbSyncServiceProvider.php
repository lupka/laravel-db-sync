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
        $this->registerDbDumpCommand();
    }


    /**
     * Register dependencies
     */
    private function registerDependencies()
    {
        $this->app->register('Collective\Remote\RemoteServiceProvider');
    }

    /**
     * Register the db:sync command.
     */
    private function registerDbSyncCommand()
    {
        $this->app->singleton('command.lupka.db-sync', function ($app) {
            return $app['Lupka\LaravelDbSync\Commands\DbSyncCommand'];
        });

        $this->commands('command.lupka.db-sync');
    }

    /**
     * Register the db:dump command.
     */
    private function registerDbDumpCommand()
    {
        $this->app->singleton('command.lupka.db-dump', function ($app) {
            return $app['Lupka\LaravelDbSync\Commands\DbDumpCommand'];
        });

        $this->commands('command.lupka.db-dump');
    }
}
