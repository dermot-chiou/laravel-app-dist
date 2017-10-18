@extends('layouts.admin')
@section('content')
    {{ Breadcrumbs::render('app-create') }}
    <div class="row">
    <form action="{{action('Admin\AppController@store')}}" method="post" class="col-md-10 col-md-offset-1">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="form-group">
            <label for="name">應用程式顯示名稱</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label for="app_id">專案代碼(限英文)</label>
            <input type="text" name="app_id" class="form-control">
        </div>

        <input type="submit" class="btn btn-primary">
    </form>
    </div>
@endsection