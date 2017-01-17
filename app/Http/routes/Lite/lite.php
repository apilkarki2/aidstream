<?php

$router->group(
    ['namespace' => 'Lite'],
    function ($router) {
        $router->group(
            ['namespace' => 'Activity'],
            function ($router) {
                $router->get(
                    '/lite/activity',
                    [
                        'as'   => 'lite.activity.index',
                        'uses' => 'ActivityController@index'
                    ]
                );
                $router->get(
                    '/lite/activity/create',
                    [
                        'as'   => 'lite.activity.create',
                        'uses' => 'ActivityController@create'
                    ]
                );
                $router->post(
                    '/lite/activity/store',
                    [
                        'as'   => 'lite.activity.store',
                        'uses' => 'ActivityController@store'
                    ]
                );
                $router->get(
                    '/lite/activity/{activity}',
                    [
                        'as'   => 'lite.activity.show',
                        'uses' => 'ActivityController@show'
                    ]
                );
                $router->get(
                    '/lite/activity/{activity}/edit',
                    [
                        'as'   => 'lite.activity.edit',
                        'uses' => 'ActivityController@edit'
                    ]
                );
                $router->get(
                    '/lite/activity/{activity}/duplicate',
                    [
                        'as'   => 'lite.activity.duplicate',
                        'uses' => 'ActivityController@duplicate'
                    ]
                );
                $router->get(
                    '/lite/activity/{activity}/delete',
                    [
                        'as'   => 'lite.activity.delete',
                        'uses' => 'ActivityController@destroy'
                    ]
                );

                $router->post(
                    '/lite/activity/{activity}/update',
                    [
                        'as'   => 'lite.activity.update',
                        'uses' => 'ActivityController@update'
                    ]
                );

                $router->get(
                    '/lite/activity/{activity}/budget/create',
                    [
                        'as'   => 'lite.activity.budget.create',
                        'uses' => 'ActivityController@createBudget'
                    ]
                );

                $router->get(
                    '/lite/activity/{activity}/budget/edit',
                    [
                        'as'   => 'lite.activity.budget.edit',
                        'uses' => 'ActivityController@editBudget'
                    ]
                );

                $router->post(
                    '/lite/activity/{activity}/budget/store',
                    [
                        'as'   => 'lite.activity.budget.store',
                        'uses' => 'ActivityController@storeBudget'
                    ]
                );

                $router->post(
                    '/lite/activity/{activity}/budget/delete',
                    [
                        'as'   => 'lite.activity.budget.delete',
                        'uses' => 'ActivityController@deleteBudget'
                    ]
                );
            }
        );
    }
);
