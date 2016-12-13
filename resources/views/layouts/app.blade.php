<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="icon" type="image/png" href="{{url('favicon.ico')}}">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link href="{{url('css/weui.min.css')}}" rel="stylesheet">
    <link href="{{url('css/vote.css')}}" rel="stylesheet">
    @stack('css')
    @stack('js')
</head>
<body>
<div class="page__hd">
    <h1 class="page__title">
        @yield('title')
    </h1>
    <p class="page__desc">@yield('description')</p>
</div>

@yield('body')
</body>
</html>