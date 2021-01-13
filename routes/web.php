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

    // Matches "/api/products/*"
    $router->group(['prefix' => 'products'], function ($router) {

        $router->get('/', 'ProductController@index');
        $router->post('/', 'ProductController@create');
        $router->get('read', 'ProductController@read'); // /read?productId=*

        // for those two endpoints pass the param "productId" in the request body
        $router->post('update', 'ProductController@update');
        $router->post('delete', 'ProductController@delete');

        // pass an array containing ids the array should be named "" 
        $router->post('/multipleDelete', 'ProductController@multipleDelete');

        // requires two params "quantity", "productId"
        $router->post('buyProduct', 'ProductController@buyProduct');
        $router->post('sellProduct', 'ProductController@sellProduct');

        $router->get('operations', 'ProductController@readOperations');

        // get top5 trending products
        $router->get('trending', 'ProductController@trendingProducts');

        //
        $router->get('lowStock' , 'ProductController@lowStockProducts');

    });

    // Matches "/api/products/*"
    $router->group(['prefix' => 'operations'], function ($router) {
        $router->get('/', 'OperationController@index');
        $router->post('/byMonth' , 'OperationController@operationByMonth');
        $router->get('/cards' , 'OperationController@cards');
        $router->get('/opResource', 'OperationController@opResource');
        $router->get('/read', 'OperationController@read');
    });

    $router->group(['prefix' => 'clients'], function ($router) {
        $router->get('/', 'ClientController@index');
        $router->post('/', 'ClientController@create');
        $router->get('/read', 'ClientController@read');
        $router->post('/update', 'ClientController@update');
        $router->post('/delete', 'ClientController@delete');
    });
});
