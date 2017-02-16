<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/18/15
 * Time: 2:38 PM
 */

use ebussola\statefull\classes\CacheFileHandler;

Route::get('/ebussola-statefull-ajax-flash-message', function() {
    return Response::json(\Flash::toArray());
});

if (!Config::get('app.debug')) {
    $cachePath = (new CacheFileHandler())->getCachePath();
    $blacklist = file_exists($cachePath . '/route-blacklist.config') ?
        file_get_contents($cachePath . '/route-blacklist.config') : null;
    $paramBlacklist = file_exists($cachePath . '/param-blacklist.config') ?
        json_decode(file_get_contents($cachePath . '/param-blacklist.config'), true) : [];

    $paramBlacklistFunctionFile = $cachePath . '/param-blacklist-function.php';
    if (file_exists($paramBlacklistFunctionFile)) {
        include $paramBlacklistFunctionFile;

        if (!isParamBlacklisted($paramBlacklist)) {
            Route::get('/{route}', function ($route) use ($cachePath) {
                \eBussola\Statefull\Plugin::$routerActive = true;

                $file = $cachePath . '/' . $route . '.html';
                if (file_exists($file)) {
                    return file_get_contents($file);
                } else {


                    try {
                        $responseRaw = file_get_contents(\Config::get('app.url') . $route . '?nocache=1');
                    }
                    catch (ErrorException $e) {
                        if (strstr($e->getMessage(), '404 Not Found')) {
                            $controller = App::make('Cms\Classes\Controller');
                            $response = $controller->run('/404');
                            $response->setStatusCode(404);

                            return $response;
                        }
                        else {
                            throw $e;
                        }
                    }


                    if (\Ebussola\Statefull\Models\Settings::get('cache_lazy_cache', false)) {
                        (new CacheFileHandler())->saveCacheFile($route, $responseRaw);
                    }

                    return $responseRaw;
                }
            })
                ->where('route', '^(?!backend)(?!combine)'. $blacklist .'.*')
            ;
        }
    }
}