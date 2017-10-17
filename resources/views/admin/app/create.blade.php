@extends('layouts.admin')
@section('content')
    <div class="alert alert-danger">
        <p>檔案上傳規則</p>
        <ul>
            <li>命名方式為 專案名稱-裝置名稱(mobile|pad).(ipa|apk) 例如： casino-pad.ipa</li>
            <li>Android 和 iOS 的應用程式 id 必須相同</li>
        </ul>
    </div>
    <form action="{{action('Admin\AppController@store')}}" method="post" enctype="multipart/form-data" class="col-md-10 col-md-offset-1">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="form-group">
            <label for="app_file">請選擇 apk 或 ipa 檔案 </label>
            <input type="file" name="app_file" id="app_file" class="form-control-file">
        </div>

        <input type="submit" class="btn btn-primary">
    </form>
@endsection