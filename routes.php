<?php
/**
 * Created by PhpStorm.
 * User: shina
 * Date: 11/18/15
 * Time: 2:38 PM
 */

Route::get('/ebussola-statefull-ajax-flash-message', function() {
    return Response::json(\Flash::toArray());
});

if (!Config::get('app.debug') && count($_GET) === 0) {
    $cachePath = App::basePath() . '/storage/statefull-cache';
    $blacklist = file_exists($cachePath . '/route-blacklist.config') ?
        file_get_contents($cachePath . '/route-blacklist.config') : null;

    Route::get('/{route}', function ($route) use ($cachePath) {
        $file = $cachePath . '/' . $route . '.html';
        if (file_exists($file)) {
            return file_get_contents($file);
        } else {




            /**
             * For development purpose, you must use a different baseURL for internal php server.
             * Because it can handle only one request at a time, you need to open 2 servers with different port or hostname.
             *
             * $responseRaw = file_get_contents(Config::get('app.url') .':8000/' . $route . '?nocache=1');
             */
            $responseRaw = file_get_contents(Config::get('app.url') .'/' . $route . '?nocache=1');





            if (\Ebussola\Statefull\Models\Settings::get('cache_lazy_cache', false)) {
                file_put_contents($file, $responseRaw);
            }

            return $responseRaw;
        }
    })
        ->where('route', '^(?!backend)(?!combine)'. $blacklist .'.*')
    ;
}