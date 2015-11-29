<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/25/15
 * Time: 12:06 PM
 */

namespace ebussola\statefull\classes;


use Illuminate\Http\RedirectResponse;

class PagesCrawler
{

    private $pageInfos = [];

    /**
     * @param null|callable $map
     * The unique param it receives id an array with page informations
     *
     * @throws \ApplicationException
     * @throws \SystemException
     */
    public function map($map = null)
    {
        $theme = \Cms\Classes\Theme::getActiveTheme();
        $pages = \Cms\Classes\Page::listInTheme($theme, true);

        foreach ($pages as $i => $page) {
            if (!isset($this->pageInfos[$page->url])) {
                $this->pageInfos[$page->url] = [
                    'url' => $page->url,
                    'content' => function() use ($page) {
                        return $this->getPageContents($page->url);
                    },
                    'pageType' => $this->getPageTypeByUrl($page->url),
                    'urlParameters' => $this->getDynamicParameters($page->url)
                ];
            }

            if (is_callable($map)) {
                $map($this->pageInfos[$page->url]);
            }
        }
    }

    /**
     * @param $url
     * @return string
     */
    public function getPageContents($url)
    {
        $controller = new \Cms\Classes\Controller();
        $response = $controller->run($url);
        return ($response instanceof \Illuminate\Http\Response) ?
            $response->getOriginalContent() : $this->renderRedirectScript($response);
    }

    /**
     * @param null|string $pageType
     * @return array
     */
    public function getPageInfos($pageType = null)
    {
        $this->map();
        if ($pageType == null) {
            return $this->pageInfos;
        }
        else {
            return array_filter($this->pageInfos, function($pageInfo) use ($pageType) {
                return $pageInfo['pageType'] == $pageType;
            });
        }
    }

    /**
     * @param $url
     * @return string[]
     */
    private function getDynamicParameters($url) {
        return array_filter(explode('/', $url), function($fragment) {
            return substr($fragment, 0, 1) == ':';
        });
    }

    /**
     * @param $url
     * @return string
     * dynamic or regular
     */
    private function getPageTypeByUrl($url) {
        return strstr($url, ':') ? 'dynamic' : 'regular';
    }

    /**
     * @param RedirectResponse $response
     * @return string
     */
    private function renderRedirectScript(RedirectResponse $response)
    {
        return '<script>window.location = "'. $response->getTargetUrl() .'"</script>';
    }

}