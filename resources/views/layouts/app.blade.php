<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Team Psikomapping') }}</title>

        <!-- PWA Manifest & Meta -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4f46e5">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Psikomapping">
        <link rel="apple-touch-icon" href="/pwa-apple-touch.png">
        <link rel="icon" type="image/png" href="/Logo.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        </style>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' })
                        .then((reg) => {
                            console.log('[PWA] Service Worker registered:', reg.scope);

                            // Check for updates
                            reg.addEventListener('updatefound', () => {
                                const newWorker = reg.installing;
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New content is available; show update toast
                                        const banner = document.getElementById('pwa-update-banner');
                                        if (banner) banner.classList.remove('hidden');
                                    }
                                });
                            });
                        })
                        .catch((err) => console.error('[PWA] SW registration failed:', err));

                    // Handle update via banner button
                    window.pwaApplyUpdate = () => {
                        navigator.serviceWorker.getRegistration().then((reg) => {
                            if (reg && reg.waiting) {
                                reg.waiting.postMessage({ type: 'SKIP_WAITING' });
                            }
                        });
                        window.location.reload();
                    };
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen relative overflow-x-hidden" x-data="{ sidebarOpen: false }">

        <!-- Light Glowing blobs (Jauh lebih ringan dengan mengurangi radius blur dan opacity) -->
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-indigo-500/5 rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-violet-500/5 rounded-full pointer-events-none"></div>

        <!-- PWA Update Banner (shown when a new version is available) -->
        <div id="pwa-update-banner" class="fixed top-4 left-1/2 -translate-x-1/2 z-[200] hidden animate-in">
            <div class="bg-indigo-600 text-white text-xs font-semibold px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-4">
                <span>✨ Pembaruan Team Psikomapping tersedia!</span>
                <button onclick="window.pwaApplyUpdate()" class="px-3 py-1 bg-white text-indigo-700 rounded-lg text-[11px] font-bold cursor-pointer hover:bg-indigo-100 transition">
                    Perbarui Sekarang
                </button>
                <button onclick="document.getElementById('pwa-update-banner').classList.add('hidden')" class="text-white/70 hover:text-white cursor-pointer">✕</button>
            </div>
        </div>

        <!-- PWA Offline Indicator -->
        <div id="pwa-offline-bar" class="fixed bottom-0 inset-x-0 z-[200] hidden">
            <div class="bg-amber-600 text-white text-xs font-semibold text-center py-2">
                📶 Koneksi terputus — Anda sedang offline. Beberapa fitur mungkin tidak tersedia.
            </div>
        </div>

        <script>
            // Offline / online indicator
            function updateOnlineStatus() {
                const bar = document.getElementById('pwa-offline-bar');
                if (!bar) return;
                if (!navigator.onLine) {
                    bar.classList.remove('hidden');
                } else {
                    bar.classList.add('hidden');
                }
            }
            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);
            updateOnlineStatus();
        </script>

        <!-- Layout Wrapper -->
        <div class="flex min-h-screen relative z-10">
            
            <!-- Sidebar Navigation (Left Panel) -->
            @include('layouts.navigation')

            <!-- Main Content Area (Right Panel) -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                
                <!-- Mobile Header (Visible only on small screens) -->
                <header class="sticky top-0 z-40 md:hidden flex items-center justify-between px-6 h-16 bg-slate-950/80 border-b border-white/5 backdrop-blur-md">
                    <!-- Mobile Brand Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg overflow-hidden shadow-md shadow-indigo-500/10 shrink-0">
                            <img src="/Logo.png" alt="Logo Team Psikomapping" class="h-full w-full object-cover">
                        </div>
                        <span class="text-sm font-bold tracking-tight text-white">Team Psikomapping</span>
                    </a>
                </header>

                <!-- Page Header (Dashboard/Project Breadcrumbs/Title) -->
                @isset($header)
                    <header class="bg-slate-950/40 border-b border-white/5 py-5 px-6 sm:px-8 relative z-10 shadow-sm">
                        <div class="w-full">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Main view slot (Fluid layout) -->
                <main class="flex-grow px-6 sm:px-8 py-8 relative z-10 w-full max-w-full">
                    {{ $slot }}
                </main>

                <!-- Global Footer inside main pane -->
                <footer class="border-t border-white/5 bg-slate-950/40 py-6 mt-12 relative z-10">
                    <div class="px-6 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-650 font-semibold">
                        <div>
                            &copy; 2026 Team Psikomapping. All rights reserved.
                        </div>
                        <div class="flex items-center gap-2">
                            <span>Workspace Version v1.0.0</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>

