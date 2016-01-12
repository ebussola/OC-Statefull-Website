<?php namespace Ebussola\Statefull\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use ebussola\statefull\classes\CacheFileHandler;
use ebussola\statefull\classes\PagesCrawler;
use Ebussola\Statefull\Models\UrlDynamic;
use League\Flysystem\Exception;

/**
 * Url Dynamics Back-end Controller
 */
class UrlDynamics extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('eBussola.Statefull', 'statefull', 'urldynamics');
    }

    public function purge()
    {
        BackendMenu::setContext('eBussola.Statefull', 'statefull', 'urlpurge');
    }

    public function onSearchDynamics()
    {
        $pageCrawler = new PagesCrawler();
        array_map(function($pageInfo) {

            if (!UrlDynamic::query()->where('url', $pageInfo['url'])->first()) {
                UrlDynamic::create([
                    'url' => $pageInfo['url'],
                    'parameters_lists' => array_map(function($item) {
                        return [
                            'name' => $item,
                            'code' => '<?php return [];'
                        ];
                    }, $pageInfo['urlParameters'])
                ]);
            }

        }, $pageCrawler->getPageInfos('dynamic'));

        return $this->listRefresh();
    }

    public function onPurge()
    {
        $data = post();
        $cacheFileHanlder = new CacheFileHandler();
        $cacheFileHanlder->deleteCacheFile($data['path'], isset($data['include_subpaths']));
    }

}