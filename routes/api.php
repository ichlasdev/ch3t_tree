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

Route::post('/register', 'UserController@register')->name('apiregister');
Route::post('/login', 'UserController@login')->name('apilogin');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/profil', 'UserController@getAuthenticatedUser')->name('profil');
    Route::post('/logout', 'UserController@logout')->name('apilogout');
    Route::get('/alluser', 'UserController@allUsers')->name('apigetuser');
    Route::post('/restatus', 'UserController@isOnline')->name('isonline');
    Route::delete('/delete/{id}','UserController@destroy')->name('apideluser');
    Route::put('/update','UserController@update')->name('apiupuser');
});

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/dashboard', 'ContactController@dashboard')->name('dashboard');
    Route::get('/contact/search', 'ContactController@search')->name('search');
    Route::post('/contact/addfriend', 'ContactController@addFriend')->name('addfriend');
    Route::delete('/contact/delfriend', 'ContactController@deleteFriend')->name('delfriend');
});

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('/message/{friend_id}', 'MessageController@showMessage')->name('showmessage');
    Route::post('/message/send/{friend_id}', 'MessageController@sendMessage')->name('sendmessage');
    Route::delete('/message/del', 'MessageController@deleteMessage')->name('delmessage');
    Route::put('/message/edit/{msg_id}', 'MessageController@editMessage')->name('upmessage');
    Route::get('/message/read/{msg_id}', 'MessageController@isRead')->name('isread');
});
