<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Proyecto Final')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    @livewireScripts
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="icon" href="{{ asset('storage/images/logo.png') }}" type="image/x-icon">
</head>

<body class="min-h-screen flex flex-col bg-base-100">
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    @livewireScripts
</body>

</html>
