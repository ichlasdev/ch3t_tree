<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::get('/book', 'BookController@book');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/bookall', 'BookController@bookAuth');
    Route::get('/user', 'UserController@getAuthenticatedUser');
    Route::post('/logout', 'UserController@logout');
    Route::get('/alluser', 'UserController@allUsers');
    Route::post('/logout', 'UserController@logout');
    Route::delete('/delete/{id}','UserController@destroy');
    Route::put('/update','UserController@update');
});
