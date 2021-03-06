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
Route::group(['middleware' => ['cros']], function () {
    Route::get('/', 'IndexController@index');

    Route::get('download', 'IndexController@index');
    Route::get('/%23/{vue_capture?}', 'IndexController@vue')->where('vue_capture', '[\/\w\.-]*');


    Route::get('apps', 'AppController@index');
    Route::get('apps/{appId}', 'AppController@show');
    Route::get('apps/{appId}/version', 'AppController@version');
    Route::get('apps/{appId}/{filename}.plist', 'PlistController@getPlist');
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


Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'IndexController@index');
    Route::get('/app', 'AppController@index');
    Route::get('/app/{appId}/edit', 'AppController@edit');
    Route::put('/app/{appId}/edit', 'AppController@update');
    Route::put('/app/{appId}/manifest', 'AppController@manifest');
    Route::get('/app/create', 'AppController@create');
    Route::post('/app/create', 'AppController@store');
    Route::get('/app/{appId}', 'AppController@show');
    Route::delete('/app/{appId}', 'AppController@destroy');
    Route::get('/app/{appId}/create', 'AppFileController@create');
    Route::post('/app/{appId}/create', 'AppFileController@store');
    Route::delete('/app/{appId}/{file}', 'AppFileController@destroy');
    Route::get('/app/{appId}/resource/create', 'AppResourceController@create');
    Route::post('/app/{appId}/resource/create', 'AppResourceController@store');
    Route::delete('/app/{appId}/resource/{resourceId}', 'AppResourceController@destroy');

});