<?php

namespace Lupka\LaravelDbSync\Commands;

use Illuminate\Console\Command;
use Collective\Remote\RemoteFacade as SSH;
use Symfony\Component\Process\Process; // TODO: require this somehow?

class DbDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps a Laravel install\'s database, used in conjunction with db:sync';

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
        $config = config('database.connections.mysql');
        $process = new Process($this->getMysqlDumpCommand($config));
		$process->setTimeout(600); // 10 minutes, should be more?
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
     * Get dump command
     *
     * @return string
     */
    public function getMysqlDumpCommand($config)
    {
        $command = sprintf('mysqldump --user=%s --password=%s --host=%s --port=%s %s',
			escapeshellarg($config['username']),
			escapeshellarg($config['password']),
			escapeshellarg($config['host']),
			escapeshellarg(3306),
			escapeshellarg($config['database'])
		);
		return $command;
    }

}
