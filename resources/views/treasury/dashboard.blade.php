<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33-1.5 1.5M21 21v-8.25" />
                        </svg>
                    </span>
                    Dashboard Keuangan
                </h2>
                <p class="text-xs text-slate-400 mt-1">Ringkasan Kas, Pool Gaji, dan Pembagian Keuangan Perusahaan</p>
            </div>
            <div class="text-xs text-slate-400 bg-white/5 border border-white/5 px-3 py-1.5 rounded-lg">
                Role Anda: <span class="font-bold text-indigo-400">{{ Auth::user()->role ? Auth::user()->role->name : 'No Role' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Saldo Kas -->
            <div class="relative overflow-hidden bg-slate-900/40 border border-white/5 rounded-2xl p-6 backdrop-blur-xl">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saldo Kas Besar</span>
                    <span class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($saldoKas, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 border-t border-white/5 pt-3">
                    <span>Kas Masuk: Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                    <span>Keluar: Rp {{ number_format($totalOut, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Pool Gaji Pokok -->
            <div class="relative overflow-hidden bg-slate-900/40 border border-white/5 rounded-2xl p-6 backdrop-blur-xl">
                <div class="absolute top-0 right-0 w-32 h-32 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pool Gaji/SDM (60%)</span>
                    <span class="p-2 bg-violet-500/10 rounded-lg text-violet-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($totalB, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 border-t border-white/5 pt-3">
                    <span>Gapok Pool (70%): Rp {{ number_format($totalGapokPool, 0, ',', '.') }}</span>
                    <span>Tukin Pool (30%): Rp {{ number_format($totalTukinPool, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Status Pembayaran Payroll -->
            <div class="relative overflow-hidden bg-slate-900/40 border border-white/5 rounded-2xl p-6 backdrop-blur-xl">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Payroll Status</span>
                    <span class="p-2 bg-amber-500/10 rounded-lg text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($payrollPending, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 border-t border-white/5 pt-3">
                    <span class="text-amber-400 font-semibold">Tertunda (Pending)</span>
                    <span class="text-emerald-400 font-semibold">Terbayar: Rp {{ number_format($payrollPaid, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Formula Visualization Section -->
        <div class="bg-slate-900/20 border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Rumus Otomatisasi Pembagian Omset</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-center">
                <div class="bg-slate-950 p-4 rounded-xl border border-white/5 flex flex-col justify-center">
                    <div class="text-[10px] font-bold text-slate-500 uppercase">A (Omset Utama)</div>
                    <div class="text-base font-extrabold text-white mt-1">100%</div>
                </div>
                <div class="flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6 rotate-90 md:rotate-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="bg-indigo-950/40 p-4 rounded-xl border border-indigo-500/20 text-left space-y-1">
                    <div class="text-[10px] font-bold text-indigo-400 uppercase text-center">B (Gaji/SDM - 60%)</div>
                    <div class="text-xs text-slate-300 font-medium">Bagi rata ke 6 Pos:</div>
                    <div class="text-[10px] text-slate-400 leading-relaxed">• 10% per Pos Divisi<br>• Pos ke-6: Penasehat & Front-man gabung</div>
                </div>
                <div class="flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6 rotate-90 md:rotate-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
                <div class="bg-emerald-950/40 p-4 rounded-xl border border-emerald-500/20 text-left space-y-1">
                    <div class="text-[10px] font-bold text-emerald-400 uppercase text-center">C (Kas & Dev - 40%)</div>
                    <div class="text-[10px] text-slate-300 leading-relaxed">
                        • **Th 1**: Dev 30% / Hasil 10%<br>
                        • **Th 2**: Dev 20% / Hasil 20%<br>
                        • **Th 3**: Dev 10% / Hasil 30%<br>
                        <strong>Bagi Hasil (Hasil):</strong><br>
                        - 50% Penasehat (Ambo)<br>
                        - 50% Saham (5 Org)
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cash Flow Transactions -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Aktivitas Keuangan Terbaru</h3>
                <a href="{{ route('treasury.cashbook') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($recentTransactions as $tx)
                    <div class="p-6 flex items-center justify-between hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-4">
                            <span class="p-2.5 rounded-lg {{ $tx->tipe === 'in' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                @if($tx->tipe === 'in')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-bold text-white">{{ $tx->deskripsi }}</p>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                    <span>{{ $tx->created_at->format('d M Y H:i') }}</span>
                                    <span>&bull;</span>
                                    <span class="capitalize px-1.5 py-0.5 bg-white/5 rounded border border-white/5">{{ $tx->kategori }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="text-base font-extrabold {{ $tx->tipe === 'in' ? 'text-emerald-450' : 'text-rose-450' }}">
                                {{ $tx->tipe === 'in' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        Belum ada riwayat aktivitas transaksi kas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
