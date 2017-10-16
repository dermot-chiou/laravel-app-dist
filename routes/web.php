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
Route::get('apps/{appId}/{filename}.plist', 'PlistController@getPlist');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'IndexController@index');
    Route::get('/app', 'AppController@index');
    Route::get('/app/{appId}/edit', 'AppController@edit');
    Route::put('/app/{appId}/edit', 'AppController@update');
    Route::get('/app/create', 'AppController@create');
    Route::post('/app/create', 'AppController@store');
    Route::get('/app/{appId}', 'AppController@show');
    Route::delete('/app/{appId}/{file?}', 'AppController@destroy');
});


// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['fw-only-whitelisted']], function () {
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');
});

Route::get('/home', 'HomeController@index')->name('home');
