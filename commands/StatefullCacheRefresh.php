<?php

namespace ebussola\statefull\commands;

use ebussola\statefull\classes\PagesCrawler;
use Ebussola\Statefull\Models\UrlBlacklist;
use Ebussola\Statefull\Models\UrlDynamic;
use Illuminate\Console\Command;

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
        // Regular Pages
        $pagesCrawler = new PagesCrawler();
        $pagesCrawler->map(function($pageInfo) {
            if ($pageInfo['pageType'] == 'regular') {
                $file = new \SplFileInfo($pageInfo['url'] . '.html');

                @mkdir($this->cachePath . $file->getPath(), 0777, true);
                file_put_contents($this->cachePath . $file->getPathname(), $pageInfo['content']());
            }
        });


        // Dynamic Pages
        UrlDynamic::all()->map(function($urlDynamic) {

            $length = count($urlDynamic->parameters_lists);
            $this->dynamicRecursiveProcess($urlDynamic->parameters_lists, 0, [
                'url' => $urlDynamic->url,
                'length' => $length
            ]);

        });



        // Index Blacklist
        @mkdir($this->cachePath, 0777, true);
        $indexBlacklist = join('',
            array_map(
                function($url) {
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
                function($url) {
                    $url = str_replace('/', '\/', trim($url, '/'));
                    return "(?!{$url})";
                },
                UrlBlacklist::all()->lists('url')
            )
        );
        file_put_contents($this->cachePath . '/route-blacklist.config', $routeBlacklist);
    }

    public function dynamicRecursiveProcess($parametersLists, $i, $data)
    {
        $list = array_values($parametersLists)[$i];
        $file = sys_get_temp_dir() . '/statefull-dynamic-list.tmp';
        file_put_contents($file, $list['code']);

        $parsedList = include $file;
        $originalUrl = $data['url'];

        foreach ($parsedList as $parsedItem) {

            $data['url'] = str_replace($list['name'], $parsedItem, $originalUrl);

            if ($i+1 < $data['length']) {
                $this->dynamicRecursiveProcess($parametersLists, $i + 1, $data);
            }

            $this->generateCacheFile($data);

            if (substr($parsedItem, -1, 1) == '?') {
                $data['url'] = rtrim(str_replace($list['name'], '', $originalUrl), '/');
                $this->generateCacheFile($data);
            }
        }
    }

    private function generateCacheFile($data) {
        $pagesCrawler = new PagesCrawler();
        $file = new \SplFileInfo($data['url'] . '.html');
        @mkdir($this->cachePath . $file->getPath(), 0777, true);

        $pageContents = $pagesCrawler->getPageContents($data['url']);

        file_put_contents($this->cachePath . $file->getPathname(), $pageContents);
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
