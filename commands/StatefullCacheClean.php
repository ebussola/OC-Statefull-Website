<?php

namespace ebussola\statefull\commands;


use ebussola\statefull\classes\CacheFileHandler;
use Illuminate\Console\Command;

class StatefullCacheClean extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'statefull:cache:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the cache of statefull pages';

    /**
     * @var CacheFileHandler
     */
    protected $cacheFileHandler;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cacheFileHandler = new CacheFileHandler();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->cacheFileHandler->deleteCacheFile('/', true);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
//			array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
//			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}