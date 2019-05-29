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

$router->post('login', ['uses' => 'AuthController@authenticate']);

// TODO: Remove register route in production
$router->post('register', ['uses' => 'AuthController@register']);

$router->group(
    ['middleware' => 'jwt.auth'],
    function () use ($router) {
        // User Info
        $router->get('user', function (Request $request) {
            return response()->json($request->auth);
        });

        // Category API
        $router->group([
            'prefix' => '/category',
        ], function () use ($router) {
            $router->get('/', 'CategoryController@index');
            $router->post('/', 'CategoryController@store');
            $router->get('/{id:[\d]+}', 'CategoryController@show');
            $router->put('/{id:[\d]+}', 'CategoryController@update');
            $router->delete('/{id:[\d]+}', 'CategoryController@destroy');
        });

        // Income API
        $router->group([
            'prefix' => '/income',
        ], function () use ($router) {
            $router->get('/', 'IncomeController@index');
            $router->post('/', 'IncomeController@store');
            $router->get('/{id:[\d]+}', 'IncomeController@show');
            $router->put('/{id:[\d]+}', 'IncomeController@update');
            $router->delete('/{id:[\d]+}', 'IncomeController@destroy');
        });

        // Expense API
        $router->group([
            'prefix' => '/expense',
        ], function () use ($router) {
            $router->get('/', 'ExpenseController@index');
            $router->post('/', 'ExpenseController@store');
            $router->get('/{id:[\d]+}', 'ExpenseController@show');
            $router->put('/{id:[\d]+}', 'ExpenseController@update');
            $router->delete('/{id:[\d]+}', 'ExpenseController@destroy');
        });
    }
);
