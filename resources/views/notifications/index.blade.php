<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-white leading-tight">🔔 Notifikasi</h2>
                <p class="text-xs text-slate-400 font-semibold mt-1">
                    {{ $unreadCount > 0 ? $unreadCount . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}
                </p>
            </div>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/10 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tandai Semua Sudah Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $isUnread = $notif->read_at === null;
                $icons = [
                    'omset_submitted' => ['icon' => '📬', 'color' => 'text-amber-400 bg-amber-500/10 border-amber-500/20'],
                    'omset_approved'  => ['icon' => '✅', 'color' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'],
                    'task_assigned'   => ['icon' => '🎯', 'color' => 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20'],
                ];
                $meta = $icons[$notif->type] ?? ['icon' => '🔔', 'color' => 'text-slate-400 bg-white/5 border-white/10'];
            @endphp
            <div class="flex items-start gap-4 p-4 rounded-2xl border transition-all
                {{ $isUnread ? 'bg-indigo-500/5 border-indigo-500/10' : 'bg-slate-900/30 border-white/5' }}">

                <!-- Icon -->
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0 border {{ $meta['color'] }}">
                    {{ $meta['icon'] }}
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-bold text-white {{ $isUnread ? 'text-white' : 'text-slate-300' }}">
                                {{ $notif->title }}
                                @if($isUnread)
                                    <span class="ml-1.5 inline-block w-2 h-2 rounded-full bg-indigo-500 align-middle"></span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">{{ $notif->body }}</p>
                            <p class="text-[10px] text-slate-600 mt-1.5 font-medium">
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($notif->action_url)
                                <a href="{{ $notif->action_url }}"
                                    class="text-[10px] px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white font-bold transition">
                                    Lihat
                                </a>
                            @endif
                            @if($isUnread)
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[10px] px-3 py-1.5 rounded-lg text-indigo-400 hover:bg-indigo-500/10 font-bold transition cursor-pointer">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="text-5xl mb-4">🔕</div>
                <h3 class="text-sm font-bold text-slate-300 mb-1">Tidak ada notifikasi</h3>
                <p class="text-xs text-slate-500">Notifikasi akan muncul saat ada pengajuan omset, persetujuan, atau penugasan task.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</x-app-layout>
