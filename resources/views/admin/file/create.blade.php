@extends('layouts.admin')
@section('content')
    <form action="{{action('Admin\AppFileController@store', [$appId])}}" method="post" enctype="multipart/form-data" class="col-md-10 col-md-offset-1">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="form-group">
            <label for="tablet">是否為平板</label>
            <input type="checkbox" name="tablet" class="form-control">
        </div>
        <div class="form-group">
            <label for="app_file">請選擇 apk 或 ipa 檔案 </label>
            <input type="file" name="app_file" id="app_file" class="form-control-file">
        </div>

        <input type="submit" class="btn btn-primary">
    </form>
@endsection