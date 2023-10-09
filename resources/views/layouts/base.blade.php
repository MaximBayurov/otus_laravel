<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="@yield('html-class')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', config('app.name'))</title>
    @section('meta')
        <meta type="keywords" content="{{config('app.name')}}">
        <meta type="description" content="{{config('app.name')}}">
    @show

    @yield('head-bottom')
</head>
<body class="@yield('body-class')">
@yield('body')
</body>
</html>
