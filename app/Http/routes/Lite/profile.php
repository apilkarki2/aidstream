<?php

$router->group(['namespace' => 'Lite\Profile'], function ($router) {
    $router->get('lite/user/profile', [
        'as'   => 'lite.user.profile.index',
        'uses' => 'ProfileController@index'
    ]);
    $router->get('lite/user/profile/edit', [
        'as'   => 'lite.user.profile.edit',
        'uses' => 'ProfileController@editProfile'
    ]);
    $router->put('lite/user/profile/store', [
        'as'   => 'lite.user.profile.store',
        'uses' => 'ProfileController@storeProfile'
    ]);
    $router->get('lite/user/username/edit', [
        'as'   => 'lite.user.username.edit',
        'uses' => 'ProfileController@editUsername'
    ]);
    $router->put('lite/user/username/store', [
        'as'   => 'lite.user.username.store',
        'uses' => 'ProfileController@storeUsername'
    ]);
    $router->get('lite/user/password/edit', [
        'as'   => 'lite.user.password.edit',
        'uses' => 'ProfileController@editPassword'
    ]);
    $router->put('lite/user/password/store', [
        'as'   => 'lite.user.password.store',
        'uses' => 'ProfileController@storePassword'
    ]);
});
