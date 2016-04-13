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
    protected $signature = 'db:sync {connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync a remote Laravel install\'s database';

    /**
     * The drip e-mail service.
     *
     * @var DripEmailer
     */
    protected $drip;

    /**
     * Create a new command instance.
     *
     * @param  DripEmailer  $drip
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
        SSH::run([
            'cd /var/www',
            'git status',
        ]);
    }
}
