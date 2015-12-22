<?php

namespace ebussola\statefull\commands;

use ebussola\statefull\classes\PagesCrawler;
use Ebussola\Statefull\Models\UrlBlacklist;
use Ebussola\Statefull\Models\UrlDynamic;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class StatefullCacheRefresh extends Command {

    const CACHE_DIR_NAME = 'statefull-cache';

    private $cachePath;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'statefull:cache:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the cache of statefull pages';

    /**
     * Whether it was chmoded or not
     *
     * @var bool
     */
    private $chmodRun = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cachePath = \App::storagePath() . '/' . self::CACHE_DIR_NAME;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $runAll = array_reduce(
            $this->option(),
            function($runAll, $option) {
                if ($runAll) {
                    return ((bool)$option === true) ? false : true;
                }

                return $runAll;
            },
            true
        );

        if ($this->option('regular') || $runAll) {
            // Regular Pages
            $pagesCrawler = new PagesCrawler();
            $pagesCrawler->map(function($pageInfo) {
                if ($pageInfo['pageType'] == 'regular') {
                    $file = new \SplFileInfo($pageInfo['url'] . '.html');

                    @mkdir($this->cachePath . $file->getPath(), 0777, true);
                    file_put_contents($this->cachePath . $file->getPathname(), $pageInfo['content']());
                }
            });
        }

        if ($this->option('dynamic') || $runAll) {
            // Dynamic Pages

            $mapUrlDynamic = function ($urlDynamic) {
                $length = count($urlDynamic->parameters_lists);
                $this->dynamicRecursiveProcess($urlDynamic->parameters_lists, 0, [
                    'url' => $urlDynamic->url,
                    'use_internal_url' => $urlDynamic->use_internal_url,
                    'internal_url' => $urlDynamic->internal_url,
                    'length' => $length
                ]);
            };

            if (count($this->option('dynamic-item')) === 0) {
                UrlDynamic::all()->map($mapUrlDynamic);
            }
            else {
                foreach ($this->option('dynamic-item') as $dynamicId) {
                    $mapUrlDynamic(UrlDynamic::find($dynamicId));
                }
            }
        }


        if ($this->option('blacklist') || $runAll) {
            // Index Blacklist
            @mkdir($this->cachePath, 0777, true);
            $indexBlacklist = join('',
                array_map(
                    function ($url) {
                        $url = str_replace('/', '\/', trim($url, '/'));
                        return "(?!\\/{$url})";
                    },
                    UrlBlacklist::all()->lists('url')
                )
            );
            file_put_contents($this->cachePath . '/index-blacklist.config', $indexBlacklist);

            // Route Blacklist
            $routeBlacklist = join('',
                array_map(
                    function ($url) {
                        $url = str_replace('/', '\/', trim($url, '/'));
                        return "(?!{$url})";
                    },
                    UrlBlacklist::all()->lists('url')
                )
            );
            file_put_contents($this->cachePath . '/route-blacklist.config', $routeBlacklist);
        }
    }

    public function dynamicRecursiveProcess($parametersLists, $i, $data)
    {
        $list = array_values($parametersLists)[$i];
        $file = sys_get_temp_dir() . '/statefull-dynamic-list.tmp';
        file_put_contents($file, $list['code']);

        $parsedList = include $file;
        $originalUrl = $data['url'];
        $originalInternalUrl = $data['internal_url'];

        if (substr($list['name'], -1, 1) == '?') {
            array_unshift($parsedList, '');
        }

        foreach ($parsedList as $parsedItem) {

            $data['url'] = rtrim(str_replace($list['name'], $parsedItem, $originalUrl), '/');

            if ($data['use_internal_url']) {
                $data['internal_url'] = rtrim(str_replace($list['name'], $parsedItem, $originalInternalUrl), '/');
            }

            if ($i+1 < $data['length']) {
                $this->dynamicRecursiveProcess($parametersLists, $i + 1, $data);
            }

            $this->generateCacheFile($data);
        }
    }

    private function generateCacheFile($data) {
        $pagesCrawler = new PagesCrawler();
        $file = new \SplFileInfo($data['url'] . '.html');
        @mkdir($this->cachePath . $file->getPath(), 0777, true);

        $pageContents = $pagesCrawler->getPageContents($data['use_internal_url'] ? $data['internal_url'] : $data['url']);

        file_put_contents($this->cachePath . $file->getPathname(), $pageContents);

        if (!$this->chmodRun) {
            $this->chmodRecursive(\App::storagePath(), 0777, 0777);
            $this->chmodRun = true;
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
			array('regular', null, InputOption::VALUE_NONE, 'Run the regular step.'),
			array('dynamic', null, InputOption::VALUE_NONE, 'Run the dynamic step.'),
			array('dynamic-item', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'ID of the Dynamic URL registered.', []),
			array('blacklist', null, InputOption::VALUE_NONE, 'Generate the blacklist file.'),
        );
    }

    private function chmodRecursive($path, $filemode, $dirmode) {
        if (is_dir($path) ) {
            if (!chmod($path, $dirmode)) {
                return;
            }

            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if($file != '.' && $file != '..') {  // skip self and parent pointing directories
                    $fullpath = $path.'/'.$file;
                    $this->chmodRecursive($fullpath, $filemode, $dirmode);
                }
            }
            closedir($dh);
        }
        else {
            if (is_link($path)) {
                return;
            }

            if (!chmod($path, $filemode)) {
                return;
            }
        }
    }

}