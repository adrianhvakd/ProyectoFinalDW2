<?php

use Livewire\Component;

new class extends Component {
    public $notifications;

    public function mount()
    {
        // Traemos las notificaciones al cargar el componente
        $this->notifications = Auth::user()->notifications()->get();
    }

    // Marca todas como leídas
    public function markAsRead()
    {
        $user = Auth::user();

        $user
            ->notifications()
            ->whereNull('leido_en')
            ->update([
                'leido_en' => now(),
            ]);

        // Refrescar la colección de notificaciones
        $this->notifications = $user->notifications()->get();
    }
};
?>
<div class="dropdown dropdown-end" wire:click="markAsRead">
    <!-- Botón -->
    <label tabindex="0" class="btn btn-ghost btn-circle relative hover:bg-base-200 transition">
        <div class="indicator">
            <span class="material-icons-outlined">notifications</span>
            @if ($notifications->count() > 0 && $notifications->whereNull('leido_en')->count() > 0)
                <span class="indicator-item badge badge-primary w-5 h-5 text-xs">{{ $notifications->count() }}</span>
            @endif
        </div>
    </label>

    <!-- Dropdown -->
    <div tabindex="0"
        class="mt-3 shadow-lg dropdown-content bg-base-200 rounded-box w-70 max-h-[500px] overflow-y-auto overflow-x-hidden p-2 lg:w-80">

        <!-- Título -->
        <div
            class="text-sm font-semibold text-base-content/90 px-2 border-b border-base-content/40 sticky top-0 bg-base-200 z-10 py-2">
            Notificaciones
        </div>

        <!-- Lista de notificaciones -->
        <ul class="menu w-full space-y-1">
            @forelse($notifications as $notification)
                <li class="menu-item">
                    <a href="#"
                        class="block px-4 py-2 rounded hover:bg-primary/10 transition flex flex-col items-start text-left">
                        <span class="font-medium">{{ $notification->titulo }}</span>
                        <span class="text-xs text-base-content/70 truncate">{{ $notification->mensaje }}</span>
                        <span
                            class="text-xs text-base-content/20 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @empty
                <li class="menu-item">
                    <span class="block px-4 py-2 text-base-content/90 text-sm text-left">No tienes notificaciones</span>
                </li>
            @endforelse
        </ul>

    </div>
</div>
