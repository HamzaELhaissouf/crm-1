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
    return $router->app->version();
});

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
    $router->post('register', 'AuthController@register');

    // Matches "/api/login
    $router->post('login', 'AuthController@login');

    $router->get('products', 'ProductController@index');
    $router->post('products', 'ProductController@create');
    $router->get('products/read', 'ProductController@read'); // products/read?productId=*

    // for those two endpoints pass the param productId in the request body
    $router->post('products/update', 'ProductController@update');
    $router->post('products/delete', 'ProductController@delete');

    // pass an array containing ids named products 
    $router->post('products/multipleDelete', 'ProductController@multipleDelete');
});
