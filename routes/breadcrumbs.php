<?php
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('首頁', action('Admin\IndexController@index'));
});

Breadcrumbs::register('app', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('App', action('Admin\AppController@index'));
});

Breadcrumbs::register('app-create', function ($breadcrumbs) {
    $breadcrumbs->parent('app');
    $breadcrumbs->push('建立APP', action('Admin\AppController@create'));
});

Breadcrumbs::register('app-show', function ($breadcrumbs, $app) {

    $breadcrumbs->parent('app');
    $breadcrumbs->push($app->app_id, action('Admin\AppController@show', [$app->app_id]));
});

Breadcrumbs::register('app-edit', function ($breadcrumbs, $app) {

    $breadcrumbs->parent('app-show', $app);
    $breadcrumbs->push('編輯', action('Admin\AppController@show', [$app->app_id]));
});


Breadcrumbs::register('file-upload', function ($breadcrumbs, $app) {

    $breadcrumbs->parent('app-show', $app);
    $breadcrumbs->push('上傳', action('Admin\AppFileController@create', [$app->app_id]));
});


Breadcrumbs::register('resource-upload', function ($breadcrumbs, $app) {

    $breadcrumbs->parent('app-show', $app);
    $breadcrumbs->push('上傳資源', action('Admin\AppResourceController@create', [$app->app_id]));
});

