<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <h2 class="font-extrabold text-2xl text-white leading-tight">
                {{ __('Ringkasan Workspace') }}
            </h2>
            <div class="text-xs text-slate-400 font-bold bg-white/5 border border-white/5 px-3 py-1 rounded-full uppercase tracking-wider">
                {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        
        <!-- Welcome Banner (Minimalist glass gradient) -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-950/60 to-slate-900/60 p-8 border border-indigo-500/10 shadow-xl">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.06),transparent)] pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl space-y-2">
                <h3 class="text-2xl font-bold text-white">Selamat datang kembali, {{ Auth::user()->name }}!</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Kelola proyek Anda, selesaikan tugas menggunakan Kanban Board interaktif, dan simpan seluruh berkas kerja Anda secara aman dalam satu tempat terintegrasi.
                </p>
            </div>
        </div>

        <!-- Stats Grid (Minimalist & Fluid) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Active Projects -->
            <div class="bg-slate-900/30 border border-white/5 p-6 rounded-2xl shadow-xl transition-all duration-300 hover:border-indigo-500/25 group">
                <div class="flex items-center space-x-4">
                    <div class="p-3.5 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Proyek Aktif</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $activeProjectsCount }}</h4>
                    </div>
                </div>
            </div>

            <!-- New Tasks -->
            <div class="bg-slate-900/30 border border-white/5 p-6 rounded-2xl shadow-xl transition-all duration-300 hover:border-amber-500/25 group">
                <div class="flex items-center space-x-4">
                    <div class="p-3.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-xl group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tugas Baru</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $todoTasksCount }}</h4>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-slate-900/30 border border-white/5 p-6 rounded-2xl shadow-xl transition-all duration-300 hover:border-blue-500/25 group">
                <div class="flex items-center space-x-4">
                    <div class="p-3.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-xl group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dalam Progres</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $inProgressTasksCount }}</h4>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-slate-900/30 border border-white/5 p-6 rounded-2xl shadow-xl transition-all duration-300 hover:border-emerald-500/25 group">
                <div class="flex items-center space-x-4">
                    <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tugas Selesai</p>
                        <h4 class="text-2xl font-bold text-white mt-1">{{ $doneTasksCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->hasRole(['Treasury', 'Head']))
            <!-- Financial Workspace Dashboard Summary (Merged) -->
            <div class="bg-slate-900/40 border border-white/5 rounded-2xl p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Ringkasan Keuangan Treasury & Payroll</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Status neraca kas besar dan alokasi dana tim terintegrasi saat ini.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-950/60 border border-white/5 p-4 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Saldo Kas Besar</span>
                            <p class="text-lg font-extrabold text-white mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
                        </div>
                        <span class="p-2 bg-emerald-500/10 rounded-lg text-emerald-450">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182" />
                            </svg>
                        </span>
                    </div>

                    <div class="bg-slate-950/60 border border-white/5 p-4 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Pool Gaji Pokok</span>
                            <p class="text-lg font-extrabold text-white mt-1">Rp {{ number_format($totalGapokPool, 0, ',', '.') }}</p>
                        </div>
                        <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372" />
                            </svg>
                        </span>
                    </div>

                    <div class="bg-slate-950/60 border border-white/5 p-4 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Payroll Pending</span>
                            <p class="text-lg font-extrabold text-amber-400 mt-1">Rp {{ number_format($payrollPending, 0, ',', '.') }}</p>
                        </div>
                        <span class="p-2 bg-amber-500/10 rounded-lg text-amber-405">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Split Grid (Urgent tasks & Recent Documents) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Urgent Tasks List -->
            <div class="bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl p-6 space-y-6">
                <div class="flex justify-between items-center pb-3 border-b border-white/5">
                    <h4 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Tugas Penting & Tenggat Terdekat
                    </h4>
                </div>

                <div class="space-y-4">
                    @forelse($urgentTasks as $task)
                        <div class="p-4 rounded-xl border border-white/5 bg-slate-950/40 hover:border-indigo-500/20 transition-all flex items-start justify-between gap-4">
                            <div class="space-y-1 min-w-0">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">
                                    {{ $task->project->name }}
                                </div>
                                <h5 class="font-bold text-sm text-slate-200 truncate">{{ $task->title }}</h5>
                                @if($task->description)
                                    <p class="text-xs text-slate-500 line-clamp-1 font-medium">{{ $task->description }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end space-y-2 shrink-0">
                                <!-- Priority Badge -->
                                <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full uppercase border
                                    @if($task->priority === 'high') bg-rose-500/10 text-rose-450 border-rose-500/20
                                    @elseif($task->priority === 'medium') bg-amber-500/10 text-amber-450 border-amber-500/20
                                    @else bg-blue-500/10 text-blue-450 border-blue-500/20 @endif">
                                    {{ $task->priority }}
                                </span>
                                <!-- Due Date -->
                                <span class="text-[11px] text-slate-400 font-semibold flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $task->due_date->isoFormat('D MMM YYYY') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-500 border border-dashed border-white/5 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-slate-650 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-semibold">Tidak ada tugas mendesak yang aktif.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Documents List -->
            <div class="bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl p-6 space-y-6">
                <div class="flex justify-between items-center pb-3 border-b border-white/5">
                    <h4 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Arsip Dokumen Terbaru
                    </h4>
                    <a href="{{ route('documents.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recentDocuments as $doc)
                        <div class="p-4 rounded-xl border border-white/5 bg-slate-950/40 hover:border-indigo-500/20 transition-all flex items-center justify-between gap-4">
                            <div class="flex items-center space-x-3 min-w-0">
                                <!-- File Icon based on mime-type -->
                                <div class="p-2.5 bg-slate-900 border border-white/5 rounded-lg text-slate-400 shrink-0">
                                    @if(str_contains($doc->mime_type, 'pdf'))
                                        <svg class="w-5 h-5 text-rose-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @elseif(str_contains($doc->mime_type, 'image'))
                                        <svg class="w-5 h-5 text-emerald-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-indigo-405" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-bold text-sm text-slate-200 truncate leading-snug">{{ $doc->title }}</h5>
                                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                        {{ round($doc->file_size / 1024, 1) }} KB &bull; {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                @foreach($doc->tags->take(1) as $tag)
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full uppercase bg-{{ $tag->color }}-500/10 text-{{ $tag->color }}-400 border border-{{ $tag->color }}-500/20">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                                <a href="{{ route('documents.download', $doc) }}" class="p-2 hover:bg-white/5 rounded-lg text-slate-400 hover:text-white transition-colors cursor-pointer" title="Unduh Dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-500 border border-dashed border-white/5 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-slate-650 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-semibold">Belum ada berkas yang diarsipkan.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
