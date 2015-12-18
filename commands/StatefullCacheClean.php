<?php

namespace ebussola\statefull\commands;


use Illuminate\Console\Command;

class StatefullCacheClean extends Command {

    private $cachePath;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cachePath = \App::storagePath() . '/' . StatefullCacheRefresh::CACHE_DIR_NAME;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $deltree = function($dir) {
            $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it,
                \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);
        };

        if (file_exists($this->cachePath)) {
            $deltree($this->cachePath);
        }
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