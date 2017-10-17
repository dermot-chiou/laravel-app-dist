@extends('layouts.admin')
@section('content')
<h1>{{$app->app_id}}</h1>
<h3>{{$app->name}}</h3>
<p>下載連結</p>
<p><a href="{{$url}}">{{$url}}</a></p>
<div class="panel panel-default">
    <div class="panel-heading" style="background-color: #F5F5F5;">
        檔案列表
    </div>
    <div class="panel-body">

        <table class="table">
            <thead>
            <tr>
                <th>檔案名稱</th>
                <th>版號</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($app->files as $file)
                <tr>
                    <td>{{$file->file_name}}</td>
                    <td>{{$file->version}}</td>
                    <td>
                        <form style="display: inline-block;" action="{{action('Admin\AppController@destroy', [$app->app_id, $file->file_name])}}" method="POST">
                            {{csrf_field()}}
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="submit" value="刪除" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <form style="display: inline-block;" action="{{action('Admin\AppController@destroy', [$app->app_id])}}" method="POST">
            {{csrf_field()}}
            <input type="hidden" name="_method" value="DELETE">
            <input type="submit" value="刪除全部" class="btn btn-danger">
        </form>
        <a href="{{action('Admin\AppController@edit', [$app->app_id])}}" class="btn btn-success">編輯</a>
        <a href="{{action('Admin\AppController@create')}}" class="btn btn-primary">上傳檔案</a>
    </div>
</div>


@endsection