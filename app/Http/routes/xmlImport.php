<?php

$router->group(
    ['namespace' => 'Complete\Xml'],
    function ($router) {
        $router->get('/xml-import', [
            'as'   => 'xml-import.index',
            'uses' => 'XmlImportController@index'
        ]);

        $router->post('xml-import', [
            'as'   => 'xml-import.store',
            'uses' => 'XmlImportController@store'
        ]);

        $router->get('/xml-import/import-status', [
            'as'   => 'xml-import.status',
            'uses' => 'XmlImportController@status'
        ]);
    }
);
