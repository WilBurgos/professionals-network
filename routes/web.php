<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "Professional Network [".$router->app->version()."]";
});

$router->group(['prefix' => 'api'], function () use ($router){
    $router->post('/login','Auth\LoginController@login');
    $router->post('/register','Auth\RegisterController@register');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->group(['prefix' => 'account'], function () use ($router){
            $router->put('/update-account','UserController@update_user');
            $router->delete('/delete-account','UserController@delete_user');
            $router->post('/import-accounts','UserController@import_users');
        });
        $router->group(['prefix' => 'relation'], function () use ($router){
            $router->post('/add-relation','RelationController@add_relation');
            $router->delete('/delete-relation','RelationController@delete_relation');
            $router->post('/relations','RelationController@get_relations');
            $router->post('/random-relations','RelationController@random_relations');
        });
    });
});


