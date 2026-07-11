<!-- DESKTOP SIDEBAR (Visible only on medium screens and up) -->
<nav class="hidden md:flex w-64 h-screen sticky top-0 bg-slate-950/40 border-r border-white/5 flex-col justify-between shrink-0 z-30">
    <div class="flex flex-col flex-grow">
        <!-- Brand / Logo Header -->
        <div class="p-6 border-b border-white/5 flex items-center gap-2.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg overflow-hidden shadow-md shadow-indigo-500/10 shrink-0">
                <img src="/Logo.png" alt="Logo" class="h-full w-full object-cover">
            </div>
            <span class="text-base font-bold tracking-tight bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">
                Team Psikomapping
            </span>
        </div>

        <!-- Desktop Navigation Menu Links -->
        <div class="flex-grow py-6 px-4 space-y-1.5 overflow-y-auto">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Dashboard</span>
            </x-nav-link>

            <!-- Workspace — All Roles -->
            <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Workspace Tasks</span>
            </x-nav-link>

            <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                </svg>
                <span>Arsip Dokumen</span>
            </x-nav-link>

            <x-nav-link :href="route('pdf-compress.index')" :active="request()->routeIs('pdf-compress.*')">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4m0 0l4 4m-4-4v9"/>
                </svg>
                <span>Compress PDF</span>
            </x-nav-link>

            <!-- Notifications (all roles) -->
            <x-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                <div class="relative shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 w-4 h-4 flex items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </div>
                <span>Notifikasi</span>
            </x-nav-link>

            @if(Auth::user()->hasRole(['Treasury', 'Headman', 'Marketing', 'Penasehat']))
                <!-- Treasury & Payroll Hub Accordion Menu -->
                <div x-data="{ open: {{ request()->routeIs('treasury.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 cursor-pointer {{ request()->routeIs('treasury.*') ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/10' : 'text-slate-400 hover:bg-white/5 hover:text-white border border-transparent' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33-1.5 1.5M21 21v-8.25" />
                            </svg>
                            <span>Treasury & Payroll</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    
                    <div x-show="open" x-collapse class="pl-6 space-y-1 mt-1" style="display: none;">
                        @if(Auth::user()->hasRole(['Treasury', 'Headman']))
                            <a href="{{ route('treasury.dashboard') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.dashboard') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                                Dashboard Keuangan
                            </a>
                        @endif

                        @if(Auth::user()->hasRole(['Treasury', 'Marketing', 'Headman']))
                            <a href="{{ route('treasury.omset') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.omset') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                                {{ Auth::user()->hasRole(['Marketing']) ? 'Ajukan Omset' : 'Omset & Persetujuan' }}
                            </a>
                        @endif

                        @if(Auth::user()->hasRole(['Treasury', 'Headman', 'Penasehat']))
                            <a href="{{ route('treasury.payroll') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.payroll') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                                Evaluasi KPI & Payroll
                            </a>
                            <a href="{{ route('treasury.cashbook') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.cashbook') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                                Buku Kas Besar
                            </a>
                        @endif

                        <a href="{{ route('treasury.events') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.events') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                            Pengeluaran Event
                        </a>

                        @if(Auth::user()->hasRole(['Treasury']))
                            <a href="{{ route('treasury.users') }}" class="block px-4 py-2 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('treasury.users') ? 'text-indigo-400 bg-white/5' : 'text-slate-450 hover:text-white' }}">
                                👑 Manajemen Pengguna
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Events visible to all roles -->
            @if(!Auth::user()->hasRole(['Treasury', 'Headman', 'Marketing', 'Penasehat']))
                <x-nav-link :href="route('treasury.events')" :active="request()->routeIs('treasury.events')">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <span>Jadwal Event</span>
                </x-nav-link>
            @endif
        </div>
    </div>

    <!-- Desktop Sidebar Footer / User Info & Actions Direct -->
    <div class="p-4 border-t border-white/5 space-y-3">
        <!-- Role Badge -->
        @if(Auth::user()->role)
            <div class="px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/10 text-center">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">
                    {{ Auth::user()->role->name }}
                </span>
            </div>
        @endif

        <!-- User Info Row -->
        <div class="flex items-center gap-3 p-1">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-md shadow-indigo-500/10">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] font-medium text-slate-550 truncate">@&ZeroWidthSpace;{{ Auth::user()->username }}</p>
            </div>
        </div>

        <!-- Quick Menu Actions (Profile & Logout) -->
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-1.5 py-2 px-3 bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white rounded-xl text-[11px] font-bold transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 01-7.5 0M4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span>Profil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-2 px-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-350 rounded-xl text-[11px] font-bold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- FLOATING BOTTOM NAVIGATION BAR WITH BEAUTIFUL POPUP DRAWER (Visible only on mobile devices) -->
<div class="md:hidden fixed bottom-5 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md" x-data="{ showMenu: false }">
    
    <!-- Pop-up Menu Sheet (Slides Upwards) -->
    <div x-cloak x-show="showMenu" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-250 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         @click.away="showMenu = false"
         class="absolute bottom-16 left-0 right-0 bg-slate-950/98 border border-white/10 backdrop-blur-2xl rounded-[32px] shadow-2xl overflow-hidden mb-3 flex flex-col"
         style="display: none;">
        
        <!-- User Info Header (matching says-app profile header style) -->
        <div class="p-5 bg-white/5 border-b border-white/10 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center font-bold text-white text-sm shadow-lg shadow-indigo-500/20">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="min-w-0 flex-1 leading-tight">
                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-500 truncate mt-0.5">@&ZeroWidthSpace;{{ Auth::user()->username }}</p>
            </div>
            @if(Auth::user()->role)
                <span class="px-2.5 py-1 text-[9px] font-extrabold bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-lg uppercase tracking-wider shrink-0">
                    {{ Auth::user()->role->name }}
                </span>
            @endif
        </div>

        <!-- Scrollable Navigation Categories (matching says-app scrollable body style) -->
        <nav class="p-5 space-y-5 max-h-[50vh] overflow-y-auto custom-scrollbar">
            <!-- Category: Utama -->
            <div class="space-y-2">
                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">Menu Utama</div>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('projects.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Workspace Tasks</span>
                </a>

                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('documents.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8" />
                    </svg>
                    <span>Arsip Dokumen</span>
                </a>

                <a href="{{ route('pdf-compress.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('pdf-compress.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414" />
                    </svg>
                    <span>Compress PDF</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('notifications.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0" />
                        </svg>
                        <span>Notifikasi</span>
                    </div>
                    @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="text-[9px] font-black bg-rose-500 text-white px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Category: Treasury & Payroll (If applicable) -->
            @if(Auth::user()->hasRole(['Treasury', 'Headman', 'Marketing', 'Penasehat']))
                <div class="space-y-2">
                    <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2 pt-2">Treasury & Payroll</div>

                    @if(Auth::user()->hasRole(['Treasury', 'Headman']))
                        <a href="{{ route('treasury.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21" />
                            </svg>
                            <span>Dashboard Keuangan</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasRole(['Treasury', 'Marketing', 'Headman']))
                        <a href="{{ route('treasury.omset') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.omset') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3" />
                            </svg>
                            <span>{{ Auth::user()->hasRole(['Marketing']) ? 'Ajukan Omset' : 'Omset & Persetujuan' }}</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasRole(['Treasury', 'Headman', 'Penasehat']))
                        <a href="{{ route('treasury.payroll') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.payroll') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6" />
                            </svg>
                            <span>Evaluasi KPI & Payroll</span>
                        </a>

                        <a href="{{ route('treasury.cashbook') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.cashbook') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" />
                            </svg>
                            <span>Buku Kas Besar</span>
                        </a>
                    @endif

                    <a href="{{ route('treasury.events') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.events') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                        <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3" />
                        </svg>
                        <span>Pengeluaran Event</span>
                    </a>

                    @if(Auth::user()->hasRole(['Treasury']))
                        <a href="{{ route('treasury.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.users') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                            <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 00-6 2.28 9.09 9.09 0 00-6-2.28" />
                            </svg>
                            <span>👑 Manajemen Pengguna</span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- Category: Jadwal Event for operasional/other roles who don't have Treasury access -->
            @if(!Auth::user()->hasRole(['Treasury', 'Headman', 'Marketing', 'Penasehat']))
                <div class="space-y-2">
                    <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2 pt-2">Operasional</div>
                    <a href="{{ route('treasury.events') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('treasury.events') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                        <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3" />
                        </svg>
                        <span>Jadwal Event</span>
                    </a>
                </div>
            @endif

            <!-- Category: Account -->
            <div class="space-y-2">
                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2 pt-2">Akun</div>
                
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('profile.edit') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-300 hover:text-white' }}">
                    <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="block w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-450 hover:bg-rose-500/10 transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Navigation Bar (Bottom Capsule) -->
    <div class="bg-slate-900/85 border border-white/10 backdrop-blur-lg rounded-2xl py-3 px-4 shadow-2xl flex items-center justify-around">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->routeIs('dashboard') ? 'text-indigo-400 font-bold scale-105' : 'text-slate-400 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
            </svg>
            <span class="text-[9px]">Beranda</span>
        </a>

        <!-- Workspace Tasks -->
        <a href="{{ route('projects.index') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->routeIs('projects.*') ? 'text-indigo-400 font-bold scale-105' : 'text-slate-400 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
            </svg>
            <span class="text-[9px]">Workspace</span>
        </a>

        @if(Auth::user()->hasRole(['Treasury', 'Headman', 'Marketing']))
            <!-- Omset Link -->
            <a href="{{ route('treasury.omset') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->routeIs('treasury.omset') ? 'text-indigo-400 font-bold scale-105' : 'text-slate-400 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818" />
                </svg>
                <span class="text-[9px]">Omset</span>
            </a>
        @endif

        <!-- Notifications Bell -->
        <a href="{{ route('notifications.index') }}" class="flex flex-col items-center gap-1 transition-all {{ request()->routeIs('notifications.*') ? 'text-indigo-400 font-bold scale-105' : 'text-slate-400 hover:text-white' }} relative">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0" />
                </svg>
                @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 flex items-center justify-center rounded-full bg-rose-500 text-[8px] font-black text-white">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </div>
            <span class="text-[9px]">Notif</span>
        </a>

        <!-- Toggle Button for Pop-up Menu -->
        <button @click="showMenu = !showMenu" class="flex flex-col items-center gap-1 transition-all text-slate-400 hover:text-white cursor-pointer" :class="showMenu ? 'text-indigo-400 scale-110' : ''">
            <svg class="w-5 h-5 transition-transform duration-200" :class="showMenu ? 'rotate-90 text-indigo-400' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <span class="text-[9px]">Lainnya</span>
        </button>
    </div>
</div>
