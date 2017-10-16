@extends('layouts.admin')
@section('content')
    <a href="{{action('Admin\AppController@create')}}" class="btn btn-primary">上傳檔案</a>
    <table class="table">
        <thead>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>link</th>
        </tr>
        </thead>
        <tbody>
        @foreach($apps as $app)
        <tr>
            <td><a href="{{action('Admin\AppController@show', [$app->app_id])}}">{{$app->app_id}}</a></td>
            <td>{{$app->name}}</td>
            <td><a href="{{$app->url}}">{{$app->url}}</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
@stop