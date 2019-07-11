<?php

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
    return $router->app->version();
});

/**
 * User
 */
$router->group(['prefix' => 'user'], function () use ($router) {
	$router->get('list', 'UserController@list');
	$router->get('info/{id}', 'UserController@info');
	$router->post('add', 'UserController@add');
	$router->put('edit', 'UserController@edit');
	$router->delete('delete', 'UserController@delete');
});

/**
 * group
 */
$router->group(['prefix' => 'group'], function () use ($router) {
	$router->get('list', 'GroupController@list');
	$router->get('info/{id}', 'GroupController@info');
	$router->post('add', 'GroupController@add');
	$router->put('edit', 'GroupController@edit');
	$router->delete('delete', 'GroupController@delete');
});

