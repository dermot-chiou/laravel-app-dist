<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>Laravel</title>

    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <style>
        body{
            margin-top: 60px;
        }
        .sidebar{
            position: fixed;
            top: 51px;
            bottom: 0;
            left: 0;
            z-index: 1000;
            display: block;
            padding: 20px;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: #f5f5f5;
            border-right: 1px solid #eee;
        }

        .main{
            adding-right: 40px;
            padding-left: 40px;
        }
    </style>
</head>
<body>
<form id="logout-form" action="{{action('Auth\LoginController@logout')}}" method="POST">
    {{csrf_field()}}
</form>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{action('Admin\IndexController@index')}}">法老 App</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">{{ Auth::user()->name }}</a></li>
                <li><a href="{{action('Auth\LoginController@logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">登出</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    @section('sidebar')
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li class="active"><a href="{{action('Admin\AppController@index')}}">App 管理 <span class="sr-only">(current)</span></a></li>
                </ul>

            </div>
            @show
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div class="row">
                <a href="{{ url()->previous() }}">上一頁</a>
                </div>
                @yield('content')
            </div>
        </div>

</div>

</body>
</html>
