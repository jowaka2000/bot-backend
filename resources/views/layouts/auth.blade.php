<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Styles -->
    <style>

    </style>

    @vite('resources/css/app.css')


</head>

<body class="bg-gray-00">

    @yield('content')
</body>

</html>
