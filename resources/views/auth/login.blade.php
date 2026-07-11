<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Team Psikomapping') }} - Masuk</title>

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
            @keyframes float-slow {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-20px) scale(1.05); }
            }
            @keyframes float-delayed {
                0%, 100% { transform: translateY(0px) scale(1.05); }
                50% { transform: translateY(20px) scale(1); }
            }
            @keyframes fade-in {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-float-slow {
                animation: float-slow 12s ease-in-out infinite;
            }
            .animate-float-delayed {
                animation: float-delayed 15s ease-in-out infinite;
            }
            .animate-fade-in {
                animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .bg-grid-pattern {
                background-size: 50px 50px;
                background-image: 
                    linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            }
        </style>
    </head>
    <body class="h-full text-slate-100 antialiased overflow-hidden">
        <div class="flex min-h-screen h-full">
            
            <!-- LEFT PANEL: Brand visual identity (Desktop only) -->
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-16 overflow-hidden border-r border-slate-900 bg-slate-950">
                <!-- Grid background -->
                <div class="absolute inset-0 bg-grid-pattern pointer-events-none"></div>
                
                <!-- Radiant Blur Blobs -->
                <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-float-slow pointer-events-none"></div>
                <div class="absolute bottom-[-15%] right-[-10%] w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[140px] animate-float-delayed pointer-events-none"></div>

                <!-- Top section: Logo and Title -->
                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl overflow-hidden shadow-lg shadow-indigo-500/20 shrink-0">
                        <img src="/Logo.png" alt="Logo" class="h-full w-full object-cover">
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-300 bg-clip-text text-transparent">
                        Team Psikomapping
                    </span>
                </div>

                <!-- Center section: Immersive Kanban Mockup -->
                <div class="relative z-10 my-auto flex items-center justify-center animate-fade-in">
                    <div class="w-full max-w-md backdrop-blur-xl bg-slate-900/40 border border-white/10 rounded-2xl p-6 shadow-2xl relative">
                        <!-- Card glow shadow outline -->
                        <div class="absolute -inset-px bg-gradient-to-br from-indigo-500/10 to-violet-500/10 rounded-2xl blur-sm pointer-events-none"></div>
                        
                        <div class="relative space-y-5">
                            <!-- Board Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-white/5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-indigo-400 uppercase tracking-widest">Active Workspace</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[11px] font-medium text-slate-400">Live Status</span>
                                </div>
                            </div>
                            
                            <!-- Tasks Stack -->
                            <div class="space-y-3">
                                <!-- Task Card 1 (Completed) -->
                                <div class="group bg-slate-900/60 border border-white/5 rounded-xl p-3.5 shadow-lg transition-all duration-300 hover:border-indigo-500/30 hover:bg-slate-900/80 hover:translate-x-1">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-900/40 rounded-full">Selesai</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Core Security</span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-200">Implementasi enkripsi berkas AES-256</p>
                                </div>
                                
                                <!-- Task Card 2 (In Progress) -->
                                <div class="group bg-slate-900/60 border border-white/5 rounded-xl p-3.5 shadow-lg transition-all duration-300 hover:border-violet-500/30 hover:bg-slate-900/80 hover:translate-x-1">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold text-violet-400 bg-violet-950/60 border border-violet-900/40 rounded-full">Dalam Progres</span>
                                        <span class="text-[10px] text-slate-500 font-medium">UI Redesign</span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-200">Desain ulang alur masuk otentikasi imersif</p>
                                    <div class="mt-3.5 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                            </svg>
                                            <span>3 / 4 Checklist</span>
                                        </div>
                                        <div class="flex -space-x-1.5">
                                            <span class="w-5 h-5 rounded-full bg-indigo-600 border border-slate-950 text-[9px] font-extrabold flex items-center justify-center text-white">JD</span>
                                            <span class="w-5 h-5 rounded-full bg-violet-600 border border-slate-950 text-[9px] font-extrabold flex items-center justify-center text-white">TH</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task Card 3 (Todo) -->
                                <div class="group bg-slate-900/60 border border-white/5 rounded-xl p-3.5 shadow-lg transition-all duration-300 hover:border-blue-500/30 hover:bg-slate-900/80 hover:translate-x-1">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold text-blue-400 bg-blue-950/60 border border-blue-900/40 rounded-full">Antrean</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Integrations</span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-200">Integrasi socket notifikasi real-time</p>
                                </div>
                            </div>

                            <!-- Progress Track -->
                            <div class="pt-2.5">
                                <div class="flex justify-between text-[11px] text-slate-400 font-bold mb-1.5">
                                    <span>Progres Rilis Sprint</span>
                                    <span>82% Selesai</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-950 rounded-full overflow-hidden p-[1px] border border-white/5">
                                    <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.5)]" style="width: 82%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom section: Slogan/Footer -->
                <div class="relative z-10 space-y-1">
                    <p class="text-sm text-slate-400 max-w-sm leading-relaxed">
                        Kelola proyek Anda, selesaikan tugas menggunakan Kanban Board interaktif, dan simpan seluruh berkas kerja Anda secara aman.
                    </p>
                    <p class="text-xs text-slate-600 pt-4">
                        &copy; 2026 Team Psikomapping. All rights reserved.
                    </p>
                </div>
            </div>

            <!-- RIGHT PANEL: Login Form (Adaptive size) -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-slate-950 relative">
                <!-- Warm ambient glow -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] bg-indigo-500/5 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute top-[20%] right-[10%] w-[200px] h-[200px] bg-violet-500/5 rounded-full blur-[80px] pointer-events-none"></div>

                <!-- Form Container Card -->
                <div class="w-full max-w-md relative z-10 bg-slate-900/30 backdrop-blur-2xl border border-slate-900 p-8 sm:p-10 rounded-3xl shadow-2xl animate-fade-in">
                    
                    <!-- Decorative corner glow -->
                    <div class="absolute -inset-px bg-gradient-to-b from-indigo-500/10 to-transparent rounded-3xl pointer-events-none"></div>
                    
                    <div class="relative space-y-8">
                        <!-- Form Header -->
                        <div class="space-y-2">
                            <!-- Mini brand logo for mobile display -->
                            <div class="flex lg:hidden items-center gap-2.5 mb-6">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg overflow-hidden shadow-md shrink-0">
                                    <img src="/Logo.png" alt="Logo" class="h-full w-full object-cover">
                                </div>
                                <span class="text-xl font-bold tracking-tight text-white">Team Psikomapping</span>
                            </div>
                            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Selamat Datang</h2>
                            <p class="text-sm text-slate-400">Silakan masuk dengan akun Anda untuk mengakses ruang kerja.</p>
                        </div>

                        <!-- Session Status Alert -->
                        @if (session('status'))
                            <div class="p-4 bg-emerald-950/40 border border-emerald-800/50 rounded-xl text-emerald-400 text-sm flex items-center gap-2.5 animate-fade-in">
                                <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <!-- Form element -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            <!-- Username Input Field -->
                            <div class="space-y-2">
                                <label for="username" class="text-xs font-semibold text-slate-300 uppercase tracking-wider block">Username</label>
                                <div class="relative">
                                    <!-- User SVG Icon -->
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input 
                                        id="username" 
                                        type="text" 
                                        name="username" 
                                        value="{{ old('username') }}" 
                                        required 
                                        autofocus 
                                        placeholder="Masukkan username Anda" 
                                        class="block w-full pl-11 pr-4 py-3.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 focus:bg-slate-950 transition-all duration-200"
                                    />
                                </div>
                                @error('username')
                                    <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1.5 animate-fade-in font-medium">
                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Input Field -->
                            <div class="space-y-2">
                                <label for="password" class="text-xs font-semibold text-slate-300 uppercase tracking-wider block">Password</label>
                                <div class="relative">
                                    <!-- Lock SVG Icon -->
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        name="password" 
                                        required 
                                        autocomplete="current-password"
                                        placeholder="••••••••" 
                                        class="block w-full pl-11 pr-12 py-3.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 focus:bg-slate-950 transition-all duration-200"
                                    />
                                    
                                    <!-- Show/Hide Toggle Button -->
                                    <button 
                                        type="button" 
                                        id="toggle-password" 
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors duration-200"
                                        title="Tampilkan / Sembunyikan Password"
                                    >
                                        <!-- Eye open icon -->
                                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <!-- Eye closed icon -->
                                        <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1.5 animate-fade-in font-medium">
                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Remember Me checkbox -->
                            <div class="flex items-center justify-between">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                                    <input 
                                        id="remember_me" 
                                        type="checkbox" 
                                        name="remember"
                                        class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-slate-900 shadow-sm"
                                    >
                                    <span class="ms-2.5 text-sm text-slate-400 font-medium hover:text-slate-300 transition-colors duration-150">Ingatkan Saya</span>
                                </label>
                            </div>

                            <!-- Login Submit Button -->
                            <div class="pt-2">
                                <button 
                                    type="submit" 
                                    class="w-full py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-500/20 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-950 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
                                >
                                    <span>Masuk ke Team Psikomapping</span>
                                    <!-- Arrow Right icon -->
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Inline JavaScript for Password Visibility Toggle -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePasswordBtn = document.getElementById('toggle-password');
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eye-icon');
                const eyeSlashIcon = document.getElementById('eye-slash-icon');

                if (togglePasswordBtn && passwordInput && eyeIcon && eyeSlashIcon) {
                    togglePasswordBtn.addEventListener('click', function() {
                        const isPassword = passwordInput.getAttribute('type') === 'password';
                        
                        // Toggle the input type
                        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                        
                        // Toggle the SVG icons
                        if (isPassword) {
                            eyeIcon.classList.add('hidden');
                            eyeSlashIcon.classList.remove('hidden');
                        } else {
                            eyeIcon.classList.remove('hidden');
                            eyeSlashIcon.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
    </body>
</html>
