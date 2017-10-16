<?php

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
    return view('index');
});

Route::get('apps', 'AppController@index');
Route::get('apps/{appId}', 'AppController@show');

Route::group(['prefix' => 'admin', 'namespace' => 'admin'], function () {
    Route::get('/', 'IndexController@index');
    Route::get('/app', 'AppController@index');
    Route::get('/app/create', 'AppController@create');
    Route::post('/app/create', 'AppController@store');
    Route::get('/app/{appId}', 'AppController@show');
    Route::delete('/app/{appId}/{file?}', 'AppController@destroy');
});

