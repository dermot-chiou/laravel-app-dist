@extends('layouts.admin')
@section('content')
    {{ Breadcrumbs::render('resource-upload', $app) }}
    <form action="{{action('Admin\AppResourceController@store', [$app->app_id])}}" method="post" enctype="multipart/form-data" class="col-md-10 col-md-offset-1">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="form-group">
            <label for="asset">請選擇檔案 </label>
            <input type="file" name="asset" id="asset" class="form-control-file">
        </div>

        <input type="submit" class="btn btn-primary">
    </form>

    <script>

    </script>
@endsection