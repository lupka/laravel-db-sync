<?php

namespace Lupka\LaravelDbSync\Commands;

use Illuminate\Console\Command;
use Collective\Remote\RemoteFacade as SSH;

class DbSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync {connection?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync a remote Laravel install\'s database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = $this->getConnection();

        $this->line('Logging into '.$connection);

        SSH::into($connection)->run([
            'cd '.$this->getRemoteDirectory($connection),
            'php artisan',
        ]);
    }

    /**
     * Gets connection name from command argument or remote config default
     *
     * @return string
     */
    protected function getConnection()
    {
        $argumentConnection = $this->argument('connection');
        if(!$argumentConnection){
            return config('remote.default');
        }
    }

    /**
     * Gets directory of Laravel install on remote machine
     *
     * @param string $connection
     * @return string
     */
    protected function getRemoteDirectory($connection)
    {
        return config('remote.connections.'.$connection.'.path');
    }
}
