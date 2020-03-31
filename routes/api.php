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

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/user', 'UserController@getAuthenticatedUser');
    Route::post('/search', 'UserController@search');
    Route::post('/logout', 'UserController@logout');
    Route::get('/alluser', 'UserController@allUsers');
    Route::delete('/delete/{id}','UserController@destroy');
    Route::put('/update','UserController@update');
});
