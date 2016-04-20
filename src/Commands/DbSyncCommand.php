<?php

namespace Lupka\LaravelDbSync\Commands;

use Illuminate\Console\Command;
use Collective\Remote\RemoteFacade as SSH;
use Symfony\Component\Process\Process; // TODO: require this somehow?
use DB;

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

        // grab the dump data
        $sqlDump = '';
        SSH::into($connection)->run([
            'cd '.$this->getRemoteDirectory($connection),
            'php artisan db:dump',
        ],
        function($line) use (&$sqlDump){
            $sqlDump .= $line;
        });

        $config = config('database.connections.mysql');
        $command = $this->getMysqlImportCommand($config, $sqlDump);
        //echo $command; die();
        $process = new Process($command);
		//$process->setTimeout(600); // 10 minutes, should be more?
		$process->run();

        if ($process->isSuccessful())
		{
            $this->line($process->getOutput());
		}
		else
		{
			$this->line($process->getErrorOutput());
		}

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


    /**
     * Get import command
     *
     * @return string
     */
    public function getMysqlImportCommand($config, $sqlDump)
    {
        $command = sprintf('mysql --user=%s --password=%s %s < %s',
			escapeshellarg($config['username']),
			escapeshellarg($config['password']),
			escapeshellarg($config['database']),
            $sqlDump
		);
        // $command = sprintf('mysql --user=%s --password=%s %s < %s',
		// 	escapeshellarg($config['username']),
		// 	escapeshellarg($config['password']),
		// 	escapeshellarg($config['database']),
        //     $sqlDump
		// );
		return $command;
    }
}
