<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'TaskVault') }} - Kelola Tugas & Dokumen Secara Aman</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-12px) scale(1.02); }
            }
            @keyframes pulse-glow {
                0%, 100% { opacity: 0.15; }
                50% { opacity: 0.3; }
            }
            .animate-float {
                animation: float 10s ease-in-out infinite;
            }
            .animate-glow-slow {
                animation: pulse-glow 8s ease-in-out infinite;
            }
            .bg-grid-pattern {
                background-size: 40px 40px;
                background-image: 
                    linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            }
        </style>
    </head>
    <body class="bg-slate-950 text-slate-200 antialiased min-h-screen relative overflow-x-hidden flex flex-col justify-between">
        
        <!-- Grid Background -->
        <div class="absolute inset-0 bg-grid-pattern pointer-events-none"></div>

        <!-- Glowing blobs -->
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[130px] animate-glow-slow pointer-events-none"></div>
        <div class="absolute top-[40%] right-[-10%] w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[150px] animate-glow-slow pointer-events-none"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[500px] h-[500px] bg-fuchsia-600/10 rounded-full blur-[130px] animate-glow-slow pointer-events-none"></div>

        <!-- Header Navigation -->
        <header class="sticky top-0 z-50 w-full backdrop-blur-md bg-slate-950/60 border-b border-white/5">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <!-- Logo & Title -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 p-0.5 shadow-md shadow-indigo-500/10 transition-transform group-hover:scale-105">
                        <svg class="h-full w-full text-white" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 6L8 13.5v11.5c0 10.5 7.5 20.3 16 23 8.5-2.7 16-12.5 16-23V13.5L24 6z" fill="white" fill-opacity="0.1" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                            <path d="M17 24l5 5 9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white group-hover:text-indigo-200 transition-colors">
                        TaskVault
                    </span>
                </a>

                <!-- Nav buttons -->
                <nav class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-semibold rounded-lg shadow-lg hover:shadow-indigo-500/10 transition-all duration-200 cursor-pointer">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-white text-xs font-semibold rounded-lg transition-all cursor-pointer">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col justify-center items-center px-6 py-16 max-w-7xl mx-auto w-full relative z-10">
            
            <!-- Hero section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center w-full">
                
                <!-- Left text column -->
                <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                        Workspace Terintegrasi & Aman
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                        Kelola Proyek & Tugas dengan <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-indigo-400 via-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                            Keamanan Maksimal
                        </span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        TaskVault menggabungkan visualisasi Kanban Board untuk kolaborasi tim yang efisien dengan penyimpanan dokumen kerja terenkripsi secara aman dalam satu platform.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                                <span>Buka Dashboard</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                                <span>Mulai Sekarang</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @endauth
                        
                        <a href="#fitur" class="px-6 py-3.5 bg-slate-900/60 border border-slate-800 hover:border-slate-700 text-slate-300 font-semibold rounded-xl hover:text-white transition-all flex items-center justify-center">
                            Pelajari Selengkapnya
                        </a>
                    </div>
                </div>

                <!-- Right preview card column -->
                <div class="lg:col-span-6 flex justify-center lg:justify-end animate-float">
                    <div class="w-full max-w-md backdrop-blur-xl bg-slate-900/40 border border-white/10 rounded-3xl p-6 shadow-2xl relative">
                        <div class="absolute -inset-px bg-gradient-to-br from-indigo-500/10 to-violet-500/10 rounded-3xl blur-sm pointer-events-none"></div>
                        
                        <!-- Header Mockup -->
                        <div class="flex items-center justify-between pb-4 border-b border-white/5 mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-semibold text-slate-400 ml-2">TaskVault Dashboard</span>
                            </div>
                        </div>

                        <!-- Stats Overview mockup -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-slate-950/60 border border-white/5 rounded-xl p-4">
                                <span class="text-[10px] uppercase font-bold text-slate-500">Active Tasks</span>
                                <p class="text-2xl font-bold text-white mt-1">12</p>
                            </div>
                            <div class="bg-slate-950/60 border border-white/5 rounded-xl p-4">
                                <span class="text-[10px] uppercase font-bold text-slate-500">Security Encrypted</span>
                                <p class="text-2xl font-bold text-emerald-400 mt-1">AES-256</p>
                            </div>
                        </div>

                        <!-- Small task mockup list -->
                        <div class="space-y-3">
                            <div class="bg-slate-950/40 border border-white/5 rounded-xl p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-violet-400"></div>
                                    <span class="text-xs font-semibold text-slate-200">Reviu Protokol Akses API</span>
                                </div>
                                <span class="text-[10px] text-violet-400 font-bold bg-violet-950/60 px-2 py-0.5 rounded-full border border-violet-900/40">In Progress</span>
                            </div>
                            <div class="bg-slate-950/40 border border-white/5 rounded-xl p-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                                    <span class="text-xs font-semibold text-slate-200">Dokumen Kebijakan Privasi</span>
                                </div>
                                <span class="text-[10px] text-emerald-400 font-bold bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-900/40">Secured</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Features Section -->
            <div id="fitur" class="w-full pt-32 pb-16 space-y-16">
                <div class="text-center space-y-3 max-w-2xl mx-auto">
                    <h2 class="text-3xl font-bold text-white tracking-tight sm:text-4xl">Dibuat untuk Pengalaman Kerja Terbaik</h2>
                    <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                        Nikmati integrasi manajemen tugas visual dengan pengamanan berkas yang terjamin kerahasiaannya.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                    <!-- Feature 1 -->
                    <div class="group bg-slate-900/30 backdrop-blur-md border border-white/5 rounded-2xl p-6.5 hover:border-indigo-500/30 hover:bg-slate-900/50 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Kanban Board Visual</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Organisasikan dan geser tugas Anda melalui kolom status Kanban interaktif untuk pemantauan progres rilis yang transparan.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group bg-slate-900/30 backdrop-blur-md border border-white/5 rounded-2xl p-6.5 hover:border-violet-500/30 hover:bg-slate-900/50 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Penyimpanan Terenkripsi</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Keamanan data prioritas kami. Semua unggahan dokumen dienkripsi untuk melindungi berkas sensitif dari akses yang tidak sah.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group bg-slate-900/30 backdrop-blur-md border border-white/5 rounded-2xl p-6.5 hover:border-fuchsia-500/30 hover:bg-slate-900/50 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 text-fuchsia-400 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Metrik & Statistik Ringkas</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Dapatkan gambaran instan tentang total proyek aktif, jumlah antrean tugas, dan tingkat penyelesaian secara seketika pada ruang kerja.
                        </p>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="w-full border-t border-white/5 bg-slate-950/80 relative z-10">
            <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-medium">
                <div>
                    &copy; 2026 TaskVault. All rights reserved.
                </div>
                <div class="flex items-center gap-2 text-slate-600">
                    <span>Laravel v{{ app()->version() }}</span>
                    <span>•</span>
                    <span>PHP v{{ PHP_VERSION }}</span>
                </div>
            </div>
        </footer>

    </body>
</html>
