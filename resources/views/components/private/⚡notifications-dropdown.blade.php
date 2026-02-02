<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $notifications;
    public $perPage = 4;
    public $page = 1;

    public function mount()
    {
        $this->notifications = collect();
        $this->loadMore();
    }

    public function loadMore()
    {
        $user = Auth::user();

        $newNotifications = $user
            ->notifications()
            ->latest()
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        $this->notifications = $this->notifications->merge($newNotifications);

        $this->page++;
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->where('id', $notificationId)->firstOrFail();

        if (is_null($notification->leido_en)) {
            $notification->update([
                'leido_en' => now(),
            ]);
        }

        return redirect()->route($notification->ruta);
    }

    public function getUnreadCountProperty()
    {
        return Auth::user()->notifications()->whereNull('leido_en')->count();
    }
};
?>

<div class="dropdown dropdown-end">
    <label tabindex="0" class="btn btn-ghost btn-circle relative hover:bg-base-200 transition">
        <div class="indicator">
            <span class="material-icons-outlined">notifications</span>
            @if ($this->unreadCount > 0)
                <span class="indicator-item badge badge-primary w-5 h-5 text-xs">
                    {{ $this->unreadCount }}
                </span>
            @endif
        </div>
    </label>

    <div tabindex="0"
        class="mt-3 shadow-lg dropdown-content bg-base-200 rounded-box w-70 max-h-[500px] overflow-y-auto overflow-x-hidden p-2 lg:w-80">

        <div
            class="text-sm font-semibold text-base-content/90 px-2 border-b border-base-content/40 sticky top-0 bg-base-200 z-10 py-2">
            Notificaciones
        </div>

        <ul class="menu w-full space-y-1">
            @forelse($notifications as $notification)
                <li class="menu-item w-full">
                    <a wire:click="markAsRead('{{ $notification->id }}')"
                        class="px-4 py-3 rounded transition flex flex-col items-start text-left gap-1 w-full
                              {{ is_null($notification->leido_en) ? 'bg-primary/20 hover:bg-primary/10' : 'bg-base-200 hover:bg-base-300' }}">

                        <span class="font-medium text-sm line-clamp-1">
                            {{ $notification->titulo }}
                        </span>

                        <span class="text-xs text-base-content/70 line-clamp-3">
                            {{ $notification->mensaje }}
                        </span>

                        <span class="text-[11px] text-base-content/40 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </a>
                </li>
            @empty
                <li class="menu-item">
                    <span class="block px-4 py-2 text-base-content/90 text-sm text-left">
                        No tienes notificaciones
                    </span>
                </li>
            @endforelse
        </ul>

        <div wire:intersect="loadMore" class="h-1"></div>
    </div>
</div>
