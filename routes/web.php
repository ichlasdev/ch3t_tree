<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::group(['middleware' => 'auth'], function() {
    Route::get('/chat', 'ChatController@index');
    Route::get('messages', 'ChatController@fetchMessages');
    Route::post('messages', 'ChatController@sendMessage');
// });

Route::get('/login', 'TestController@login')->name('login');
Route::get('/register', 'TestController@register')->name('register');
Route::get('/sukses', 'TestController@sukses');
Route::get('/update', 'TestController@update');
