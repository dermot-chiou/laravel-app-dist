@extends('layouts.admin')
@section('content')
    <h1>{{$app->app_id}}</h1>
    <h3>{{$app->name}}</h3>


    <form action="{{action('Admin\AppController@update', [$app->app_id])}}" method="POST">
        {{csrf_field()}}
        <input type="hidden" name="_method" value="PUT">
        <label for="description">描述</label>
        <textarea name="description" id="description" cols="30" rows="10" class="form-control">{{$app->description}}</textarea>
        <input type="submit" value="送出" class="btn btn-primary">
    </form>
@endsection