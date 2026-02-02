<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @vite('resources/css/app.css')
    @stack('scripts')
    @livewireStyles()
</head>

<body class="m-0 bg-black">
    @yield('content')
    @livewireScripts()
</body>

</html>
