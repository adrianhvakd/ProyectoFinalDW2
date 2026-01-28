<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base-100">
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />

        <!-- Drawer Content -->
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Navbar -->
            <nav class="navbar bg-base-300 px-4 shadow-md gap-2">
                <!-- Menu button -->
                <label for="my-drawer-4" aria-label="toggle sidebar" class="btn btn-square btn-ghost">
                    <span class="material-icons-outlined text-xl">menu</span>
                </label>

                <!-- Page Title -->
                <div class="px-4 text-lg md:text-xl font-bold text-primary">
                    @yield('title')
                </div>

                <div class="flex-1"></div>

                <!-- Notificaciones Dropdown -->
                <livewire:private.notifications-dropdown />



                <!-- User Dropdown -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar hover:bg-base-200 transition">
                        <div class="w-10 rounded-full">
                            <img src="{{ $currentUser->avatar ?? 'https://i.pravatar.cc/150?u=' . $currentUser->id }}"
                                alt="Avatar" />
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

            <!-- Page content -->
            <main class="p-4 flex-1 bg-base-100">
                @yield('content')
            </main>
        </div>

        <!-- Drawer Sidebar -->
        <div class="drawer-side is-drawer-close:overflow-visible">
            <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside
                class="flex min-h-full flex-col bg-base-200 transition-all duration-300
                          is-drawer-close:w-16 is-drawer-open:w-64">
                <ul class="menu w-full grow space-y-2 p-2">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full rounded-lg hover:bg-primary/10 flex items-center px-2 space-x-2"
                            data-tip="Inicio">
                            <span class="material-icons-outlined text-lg">home</span>
                            <span class="is-drawer-close:hidden font-medium">Inicio</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('catalogo') }}"
                            class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full rounded-lg hover:bg-primary/10 flex items-center px-2 space-x-2"
                            data-tip="Catálogo">
                            <span class="material-icons-outlined text-lg">library_books</span>
                            <span class="is-drawer-close:hidden font-medium">Catálogo</span>
                        </a>
                    </li>

                    <li>
                        <button
                            class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full rounded-lg hover:bg-primary/10 flex items-center px-2 space-x-2"
                            data-tip="Mis Documentos">
                            <span class="material-icons-outlined text-lg">folder</span>
                            <span class="is-drawer-close:hidden font-medium">Mis Documentos</span>
                        </button>
                    </li>

                    <li>
                        <button
                            class="is-drawer-close:tooltip is-drawer-close:tooltip-right h-10 w-full rounded-lg hover:bg-primary/10 flex items-center px-2 space-x-2"
                            data-tip="Historial de pagos">
                            <span class="material-icons-outlined text-lg">history</span>
                            <span class="is-drawer-close:hidden font-medium">Historial de pagos</span>
                        </button>
                    </li>

                    <li class="mt-auto">

                        <form action="{{ route('logout') }}" method="POST" onclick="this.submit()"
                            class="is-drawer-close:tooltip is-drawer-close:tooltip-right btn btn-error btn-outline btn-sm w-full justify-start h-10"
                            data-tip="Cerrar Sesión">
                            @csrf
                            <span class="material-icons-outlined text-center items-center py-1">logout</span>
                            <span class="is-drawer-close:hidden font-medium">Cerrar Sesión</span>
                        </form>
                    </li>
                </ul>
            </aside>
        </div>
    </div>
</body>

</html>
