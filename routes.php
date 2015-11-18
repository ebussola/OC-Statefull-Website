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