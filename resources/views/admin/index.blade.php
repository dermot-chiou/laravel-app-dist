<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">

        <title>Laravel</title>
        
        <link rel="stylesheet" href="{{asset('css/app.css')}}">
        
        
    </head>
    <body>
    <form action="{{action('Admin\IndexController@store')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <input type="file" name="app_file" multiple>
        <input type="submit">
   </form>
    </body>
</html>
