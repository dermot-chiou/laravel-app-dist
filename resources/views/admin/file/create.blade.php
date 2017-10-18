@extends('layouts.admin')
@section('content')
    {{ Breadcrumbs::render('file-upload', $app) }}
    <form action="{{action('Admin\AppFileController@store', [$app->app_id])}}" method="post" enctype="multipart/form-data" class="col-md-10 col-md-offset-1">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="form-group">
            <label for="tablet">是否為平板</label>
            <input type="checkbox" name="tablet" class="form-control">
        </div>
        <div class="form-group">
            <label for="app_file">請選擇 apk 或 ipa 檔案 </label>
            <input type="file" name="app_file" id="app_file" class="form-control-file" accept=".ipa,.apk">
        </div>

        <input type="submit" class="btn btn-primary">
    </form>

    <script>
        $(function () {
            $('#app_file').change(function (e) {
                var vailds = ['.ipa', '.apk'];
                var ext = this.value.substring(this.value.lastIndexOf('.'));
                if(vailds.indexOf(ext) < 0)
                {
                    this.value = null;
                    alert('請選擇正確的檔案');
                    return false;
                }
                return true;
            });
        })
    </script>
@endsection