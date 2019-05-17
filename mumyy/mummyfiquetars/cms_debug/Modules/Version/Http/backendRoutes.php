<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/version'], function (Router $router) {
    $router->bind('version', function ($id) {
        return app('Modules\Version\Repositories\VersionRepository')->find($id);
    });
    $router->get('versions', [
        'as' => 'admin.version.version.index',
        'uses' => 'VersionController@index',
        'middleware' => 'can:version.versions.index'
    ]);
    $router->post('versions', [
        'as' => 'admin.version.version.update',
        'uses' => 'VersionController@update',
        'middleware' => 'can:version.versions.update'
    ]);

// append

});
