<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">

        <title>PHARAOH APP</title>
        
        <link rel="stylesheet" href="{{asset('css/app.css').'?v=1.0.3'}}">

    </head>
    <body>
        <div id="app">
            
        </div>
        <script type="text/javascript" src="{{asset('js/app.js').'?v=1.0.3'}}"></script>
    </body>
</html>
