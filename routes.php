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


$cachePath = App::basePath() . '/storage/statefull-cache';
$blacklist = file_exists($cachePath . '/route-blacklist.config') ?
    file_get_contents($cachePath . '/route-blacklist.config') : null;

Route::get('/{route}', function($route) use ($cachePath) {
    $file = $cachePath . '/' . $route . '.html';
    if (file_exists($file)) {
        return file_get_contents($file);
    }
    else {
        return (new \Cms\Classes\Controller())->run('/' . $route);
    }
})
    ->where('route', '^(?!backend)(?!combine)'. $blacklist .'.*')
;