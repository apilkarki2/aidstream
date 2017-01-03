<?php

$router->group(['namespace' => 'Lite'], function ($router) {
    $router->group(['namespace' => 'Activity',], function ($router) {
        $router->get('/lite/activity', [
            'as'   => 'lite.activity.index',
            'uses' => 'ActivityController@index'
        ]);
    });
});
