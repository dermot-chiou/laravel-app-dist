@extends('layouts.admin')
@section('content')
{{ Breadcrumbs::render('app-show', $app) }}
<h1>{{$app->app_id}}</h1>
<h3>{{$app->name}}</h3>
<p>下載連結</p>
<p><a href="{{$url}}">{{$url}}</a></p>
<p>版本檢查 API</p>
<p><a href="{{action('AppController@version', [$app->app_id])}}">{{action('AppController@version', [$app->app_id])}}</a></p>
<div class="panel panel-default">
    <div class="panel-heading" style="background-color: #F5F5F5;">
        檔案列表
    </div>
    <div class="panel-body">

        <table class="table">
            <thead>
            <tr>
                <th>檔案名稱 (原始檔名)</th>
                <th>版號</th>
                <th>平板</th>
                <th>識別碼</th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody>
            @foreach($app->files as $file)
                <tr>
                    <td>{{$file->file_name}} ({{$file->original_name}})</td>
                    <td>{{$file->version}}</td>
                    <td>{{$file->tablet ? '是' : '否'}}</td>
                    <td>{{$file->bundle_id}}</td>
                    <td>
                        <form style="display: inline-block;" action="{{action('Admin\AppFileController@destroy', [$app->app_id, $file->file_name])}}" method="POST">
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
        <a href="{{action('Admin\AppFileController@create', [$app->app_id])}}" class="btn btn-primary">上傳檔案</a>

    </div>
</div>

<div class="panel panel-success">
    <div class="panel-heading">
        Manifest
    </div>
    <form action="{{action('Admin\AppController@manifest', [$app->app_id])}}" method="POST">
        <input type="hidden" name="_method" value="PUT">
        {{ csrf_field() }}
        <div class="panel-body">
            @if ($errors->has('manifest'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->get('manifest') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <textarea name="manifest" id="manifest" cols="30" rows="10" class="form-control textbox" placeholder="請輸入JSON字串">{{(old('manifest') !== null) ? old('manifest') : $app->manifest}}</textarea>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary">
        </div>
    </form>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        資源列表
    </div>
    <div class="panel-body">

        <table class="table">
            <thead>
            <tr>
                <th>檔案名稱</th>
                <th>CND 連結</th>
                <th>Disk 連結</th>
                <th>MD5 Hash</th>
                <th>sha1 Hash</th>
                <th>刪除</th>
            </tr>
            </thead>
            <tbody>
            @foreach($app->resources as $resource)
                <tr>
                    <td>{{basename($resource->path)}}</td>
                    <td><a href="{{cdn($resource->path, $disk->url(ltrim($resource->path,'/')))}}">CDN</a></td>
                    <td><a href="{{$disk->url(ltrim($resource->path,'/'))}}">Disk</a></td>
                    <td>{{$resource->md5}}</td>
                    <td>{{$resource->sha1}}</td>
                    <td>
                        <form style="display: inline-block;" action="{{action('Admin\AppResourceController@destroy', [$app->app_id, $resource->id])}}" method="POST">
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
        <a href="{{action('Admin\AppResourceController@create', [$app->app_id])}}" class="btn btn-primary">上傳資源</a>
    </div>
</div>

    <script>
        $(document).delegate('.textbox', 'keydown', function(e) {
            var keyCode = e.keyCode || e.which;

            if (keyCode == 9) {
                e.preventDefault();
                var start = this.selectionStart;
                var end = this.selectionEnd;

                // set textarea value to: text before caret + tab + text after caret
                $(this).val($(this).val().substring(0, start)
                        + "\t"
                        + $(this).val().substring(end));

                // put caret at right position again
                this.selectionStart =
                        this.selectionEnd = start + 1;
            }
        });
        
        $('.textbox').change(function (e) {
            try {
                var $this = $(this);
                var $val = JSON.parse($this.val());
                $this.val(JSON.stringify($val, undefined, 4));
            }
            catch (err)
            {
                alert(err.message);
            }
        });
    </script>

@endsection