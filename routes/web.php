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
use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return response()->json(["version" => "1.0"]);
});

$router->post(
    'login', ['uses' => 'AuthController@authenticate']
);

$router->group(
    ['middleware' => 'jwt.auth'],
    function () use ($router) {
        $router->get('user', function (Request $request) {
            return response()->json($request->auth);
        });

        $router->group([
            'prefix' => '/category',
        ], function () use ($router) {
            $router->get('/', 'CategoryController@index');
            $router->post('/', 'CategoryController@store');
            $router->get('/{id:[\d]+}', 'CategoryController@show');
            $router->put('/{id:[\d]+}', 'CategoryController@update');
            $router->delete('/{id:[\d]+}', 'CategoryController@destroy');
        });
    }
);
