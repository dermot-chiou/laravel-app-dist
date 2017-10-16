@extends('layouts.admin')
@section('content')
<h1>{{$app->app_id}}</h1>
<h3>{{$app->name}}</h3>
<p>下載連結</p>
<p>{{$url}}</p>
<a href="{{action('Admin\AppController@create')}}" class="btn btn-primary">上傳檔案</a>
<h4>File list</h4>
<ul>
    @foreach($app->files as $file)
        <li>
            {{$file->file_name}}: <span class="badge badge-success">ver. {{$file->version}}</span>
            <form style="display: inline-block;" action="{{action('Admin\AppController@destroy', [$app->app_id, $file->file_name])}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="DELETE">
                <input type="submit" value="刪除" class="btn btn-danger">
            </form>
        </li>
    @endforeach
</ul>
<form action="{{action('Admin\AppController@destroy', [$app->app_id])}}" method="POST">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="DELETE">
    <input type="submit" value="刪除全部" class="btn btn-danger">
</form>
@endsection