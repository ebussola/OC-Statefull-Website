<?php

namespace ebussola\statefull\commands;

use ebussola\statefull\classes\CacheFileHandler;
use ebussola\statefull\classes\PagesCrawler;
use Ebussola\Statefull\Models\GetParamBlacklist;
use Ebussola\Statefull\Models\UrlBlacklist;
use Ebussola\Statefull\Models\UrlDynamic;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class StatefullCacheRefresh extends Command {

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
     * @var CacheFileHandler
     */
    protected $cacheFileHandler;

    /**
     * Property used to store current variable values
     *
     * @var array
     */
    protected $currentVars = [];

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
                    $this->cacheFileHandler->saveCacheFile($pageInfo['url'], $pageInfo['content']());
                }
            });
        }

        if ($this->option('dynamic') || $runAll) {
            // Dynamic Pages

            $mapUrlDynamic = function ($urlDynamic) {
                $length = count($urlDynamic->parameters_lists);
                if ($length > 0) {
                    $this->dynamicRecursiveProcess($urlDynamic->parameters_lists, 0, [
                        'url' => $urlDynamic->url,
                        'use_internal_url' => $urlDynamic->use_internal_url,
                        'internal_url' => $urlDynamic->internal_url,
                        'length' => $length
                    ]);
                }
                else {
                    $this->generateCacheFile([
                        'url' => $urlDynamic->url,
                        'use_internal_url' => $urlDynamic->use_internal_url,
                        'internal_url' => $urlDynamic->internal_url,
                        'length' => $length
                    ]);
                }
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
            @mkdir($this->cacheFileHandler->getCachePath(), 0777, true);

            // Index Blacklist
            $indexBlacklist = join('',
                array_map(
                    function ($url) {
                        $url = str_replace('/', '\/', trim($url, '/'));
                        return "(?!\\/{$url})";
                    },
                    UrlBlacklist::all()->lists('url')
                )
            );
            file_put_contents($this->cacheFileHandler->getCachePath() . '/index-blacklist.config', $indexBlacklist);

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
            file_put_contents($this->cacheFileHandler->getCachePath() . '/route-blacklist.config', $routeBlacklist);

            // Param Blacklist
            $paramBlacklist = GetParamBlacklist::all();
            $paramBlacklist->push([
                'name' => 'nocache',
                'values' => json_encode([
                    ['value' => '1']
                ])
            ]);
            file_put_contents($this->cacheFileHandler->getCachePath() . '/param-blacklist.config', $paramBlacklist->toJson());

            // Param Blacklist function
            copy(\App::pluginsPath() . '/ebussola/statefull/assets/param-blacklist-function.php', $this->cacheFileHandler->getCachePath() . '/param-blacklist-function.php');
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
            $this->currentVars[$list['name']] = $parsedItem;

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
        $pageContents = $pagesCrawler->getPageContents($data['use_internal_url'] ? $data['internal_url'] : $data['url']);

        $this->cacheFileHandler->saveCacheFile($data['url'], $pageContents);
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

}
