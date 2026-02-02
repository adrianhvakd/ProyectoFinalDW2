<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
    <link rel="icon" href="{{ asset('storage/images/logo.png') }}" type="image/x-icon">
</head>

<body class="bg-base-100">
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col min-h-screen">
            <nav class="navbar bg-base-300 px-4 shadow-md gap-2 sticky top-0 z-50">
                <label for="my-drawer-4" aria-label="toggle sidebar" class="btn btn-square btn-ghost">
                    <span class="material-icons-outlined text-xl">menu</span>
                </label>

                <div class="px-4 text-lg hidden lg:block font-bold text-primary ">
                    @yield('title')
                </div>

                <div class="flex-1"></div>

                <label class="swap swap-rotate btn btn-ghost btn-circle">
                    <input type="checkbox" id="theme-toggle" />

                    <span class="material-icons-outlined swap-off text-xl">
                        light_mode
                    </span>

                    <span class="material-icons-outlined swap-on text-xl">
                        dark_mode
                    </span>
                </label>

                @if (auth()->user()->role == 'user')
                    @livewire('private.carrito')
                @endif
                <livewire:private.notifications-dropdown />

                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar hover:bg-base-200 transition">
                        <div class="w-10 rounded-full">
                            <img src="{{ asset('storage/images/profile/' . $currentUser->profile_picture) ?? 'https://i.pravatar.cc/150?u=' . $currentUser->id }}"
                                alt="Avatar" draggable="false" />
                        </div>
                    </label>
                    <ul tabindex="0"
                        class="mt-3 p-2 shadow-lg menu menu-compact dropdown-content bg-base-200 rounded-box w-50">
                        <div class="px-2 py-2 border-b border-base-content/70">
                            <p class="font-bold text-xs text-base-content/70">{{ $currentUser->name }}</p>
                            <p class="text-xs text-base-content/40">{{ $currentUser->email }}</p>
                        </div>
                        <li><a href="#" class="hover:bg-primary/20">Perfil</a></li>
                        <li><a href="#" class="hover:bg-primary/20">Configuración</a></li>
                        <li class="hover:bg-error/20 w-full p-0">
                            <form action="{{ route('logout') }}" method="POST" class="w-full cursor-pointer"
                                onclick="this.submit()">
                                @csrf
                                Cerrar sesión
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="p-4 flex-1 bg-base-100">
                @yield('content')
            </main>
        </div>

        <div class="drawer-side is-drawer-close:overflow-visible top-0 z-50">
            <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
            @if ($currentUser->role == 'user')
                <aside
                    class="flex min-h-full flex-col bg-base-200 transition-all duration-300
               is-drawer-close:w-16 is-drawer-open:w-64">

                    <div
                        class="flex flex-col items-center justify-center h-28 is-drawer-close:h-16 border-b border-base-content/20">
                        <a href="{{ route('dashboard') }}" draggable="false">
                            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo"
                                class="object-contain transition-all duration-300 is-drawer-close:h-8 is-drawer-close:w-8 h-20 w-20"
                                draggable="false">
                        </a>
                        <p class="text-xs font-bold is-drawer-close:hidden">Norm<span
                                class="font-bold text-primary">Flow</span></p>
                    </div>

                    <ul class="menu w-full grow space-y-2 px-2 pt-2">

                        <li class="w-full">
                            <a href="{{ route('dashboard') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Dashboard">
                                <span class="material-icons-outlined text-lg">home</span>
                                <span class="is-drawer-close:hidden font-medium">Dashboard</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('catalogo') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Catálogo">
                                <span class="material-icons-outlined text-lg">library_books</span>
                                <span class="is-drawer-close:hidden font-medium">Catálogo</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('mis-documentos') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Mis Documentos">
                                <span class="material-icons-outlined text-lg">description</span>
                                <span class="is-drawer-close:hidden font-medium">Mis Documentos</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('historial') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Historial de pagos">
                                <span class="material-icons-outlined text-lg">history</span>
                                <span class="is-drawer-close:hidden font-medium">Historial de pagos</span>
                            </a>
                        </li>

                        <li class="mt-auto">
                            <form action="{{ route('logout') }}" method="POST" onclick="this.submit()"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right btn btn-error btn-outline btn-sm w-full h-10"
                                data-tip="Cerrar Sesión">
                                @csrf
                                <span class="material-icons-outlined text-center items-center py-1">logout</span>
                                <span class="is-drawer-close:hidden font-medium">Cerrar Sesión</span>
                            </form>
                        </li>
                    </ul>
                </aside>
            @else
                <aside
                    class="flex min-h-full flex-col bg-base-200 transition-all duration-300
               is-drawer-close:w-16 is-drawer-open:w-64">

                    <div
                        class="flex flex-col items-center justify-center h-28 is-drawer-close:h-16 border-b border-base-content/20">
                        <a href="{{ route('admin-dashboard') }}" draggable="false">
                            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo"
                                class="object-contain transition-all duration-300
                       is-drawer-close:h-8 is-drawer-close:w-8
                       h-20 w-20"
                                draggable="false">
                        </a>
                        <p class="text-xs font-bold is-drawer-close:hidden">Norm<span
                                class="font-bold text-primary">Flow</span></p>
                    </div>

                    <ul class="menu w-full grow space-y-2 px-2 pt-2">

                        <li class="w-full">
                            <a href="{{ route('admin-dashboard') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Dashboard">
                                <span class="material-icons-outlined text-lg">home</span>
                                <span class="is-drawer-close:hidden font-medium">Dashboard</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('admin-usuarios') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Usuarios">
                                <span class="material-icons-outlined text-lg">person</span>
                                <span class="is-drawer-close:hidden font-medium">Usuarios</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('admin-pagos') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Pagos">
                                <span class="material-icons-outlined text-lg">attach_money</span>
                                <span class="is-drawer-close:hidden font-medium">Pagos</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('admin-accesos') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Accesos">
                                <span class="material-icons-outlined text-lg">lock</span>
                                <span class="is-drawer-close:hidden font-medium">Accesos</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('admin-documentos') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Documentos">
                                <span class="material-icons-outlined text-lg">description</span>
                                <span class="is-drawer-close:hidden font-medium">Documentos</span>
                            </a>
                        </li>

                        <li class="w-full">
                            <a href="{{ route('admin-reportes') }}"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full flex items-center gap-2 rounded-lg hover:bg-primary/10"
                                data-tip="Reportes">
                                <span class="material-icons-outlined text-lg">pie_chart</span>
                                <span class="is-drawer-close:hidden font-medium">Reportes</span>
                            </a>
                        </li>

                        <li class="mt-auto">
                            <form action="{{ route('logout') }}" method="POST" onclick="this.submit()"
                                class="is-drawer-close:tooltip is-drawer-close:tooltip-right btn btn-error btn-outline btn-sm w-full h-10"
                                data-tip="Cerrar Sesión">
                                @csrf
                                <span class="material-icons-outlined text-center items-center py-1">logout</span>
                                <span class="is-drawer-close:hidden font-medium">Cerrar Sesión</span>
                            </form>
                        </li>
                    </ul>
                </aside>
            @endif
        </div>


    </div>
    @livewireScripts()
    <script>
        const html = document.documentElement;
        const toggle = document.getElementById('theme-toggle');

        if (toggle && html.getAttribute('data-theme') != localStorage.getItem('theme')) {
            const savedTheme = localStorage.getItem('theme');
            html.setAttribute('data-theme', savedTheme);
            toggle.checked = savedTheme === 'dark';

            toggle.addEventListener('change', () => {
                const theme = toggle.checked ? 'dark' : 'light';
                html.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            });
        }
    </script>

</body>

</html>
