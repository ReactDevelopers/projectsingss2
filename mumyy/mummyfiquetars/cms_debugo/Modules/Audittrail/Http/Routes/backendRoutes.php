<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/audittrail'], function (Router $router) {


        $router->get('logs',['uses'=>'LogController@index','as'=>'admin.audittrail.log.index']);
        $router->get('logs/create',['uses'=>'LogController@create','as'=>'admin.audittrail.log.create']);
        $router->post('logs',['uses'=>'LogController@store','as'=>'admin.audittrail.log.store']);
        $router->get('logs/{id}/edit',['uses'=>'LogController@edit','as'=>'admin.audittrail.log.edit']);
        $router->put('logs/{id}',['uses'=>'LogController@update','as'=>'admin.audittrail.log.update']);
        $router->delete('logs/{id}',['uses'=>'LogController@destroy','as'=>'admin.audittrail.log.destroy']);
        $router->get('logs/export-csv',['uses'=>'LogController@getExportVendors','as'=>'admin.audittrail.log.exportcsv']);

// append

});
