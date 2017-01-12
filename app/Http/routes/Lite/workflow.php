<?php

$router->group(['namespace' => 'Lite\Workflow'], function ($router) {
    $router->post('lite/activity/{activity}/complete', [
        'as'   => 'lite.activity.complete',
        'uses' => 'WorkflowController@complete'
    ]);
    $router->post('lite/activity/{activity}/verify', [
        'as'   => 'lite.activity.verify',
        'uses' => 'WorkflowController@verify'
    ]);
    $router->post('lite/activity/{activity}/publish', [
        'as'   => 'lite.activity.publish',
        'uses' => 'WorkflowController@publish'
    ]);
});
