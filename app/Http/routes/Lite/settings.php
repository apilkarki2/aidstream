<?php

$router->group(['namespace' => 'Lite\Settings'], function ($router) {
    $router->get('lite/settings', [
        'as'   => 'lite.settings.index',
        'uses' => 'SettingsController@index'
    ]);
    $router->get('lite/settings/create', [
        'as'   => 'lite.settings.create',
        'uses' => 'SettingsController@create'
    ]);
    $router->get('lite/settings/edit', [
        'as'   => 'lite.settings.edit',
        'uses' => 'SettingsController@edit'
    ]);
});
