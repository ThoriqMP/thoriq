<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 01-7.5 0M4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    Evaluasi KPI & Distribusi Payroll
                </h2>
                <p class="text-xs text-slate-400 mt-1">Pembagian 70% Gaji Pokok Secara Rata dan 30% Tukin Berdasarkan Grade KPI (7 SDM)</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{ showConfirmModal: false, formToSubmit: null }">
        <!-- Choose Approved Omset Log -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl p-6">
            <form action="{{ route('treasury.payroll') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Pilih Log Omset Tanggal (Status Approved)</label>
                    <select name="omset_log_id" onchange="this.form.submit()" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Log Omset --</option>
                        @foreach($omsetLogs as $log)
                            <option value="{{ $log->id }}" {{ $selectedLog && $selectedLog->id == $log->id ? 'selected' : '' }}>
                                Omset: Rp {{ number_format($log->nominal_omset, 0, ',', '.') }} (Tanggal: {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($selectedLog)
            <!-- Budget Allocation Summary for Selected Omset -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-slate-900/20 border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-550 uppercase">Nominal Omset (A)</p>
                    <p class="text-lg font-extrabold text-white mt-1">Rp {{ number_format($selectedLog->nominal_omset, 0, ',', '.') }}</p>
                </div>
                <div class="bg-indigo-950/20 border border-indigo-500/10 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-indigo-400 uppercase">Anggaran Payroll (B - 70%)</p>
                    <p class="text-lg font-extrabold text-indigo-350 mt-1">Rp {{ number_format($selectedLog->alokasi_gaji, 0, ',', '.') }}</p>
                </div>
                <div class="bg-violet-950/20 border border-violet-500/10 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-violet-400 uppercase">Pool Gapok (70% B)</p>
                    <p class="text-lg font-extrabold text-violet-350 mt-1">Rp {{ number_format($selectedLog->gaji_pokok_pool, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-500 mt-1">Dibagi rata ke 7 SDM (@Rp {{ number_format($selectedLog->gaji_pokok_pool / 7, 0, ',', '.') }})</p>
                </div>
                <div class="bg-emerald-950/20 border border-emerald-500/10 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-emerald-400 uppercase">Pool Tukin (30% B)</p>
                    <p class="text-lg font-extrabold text-emerald-350 mt-1">Rp {{ number_format($selectedLog->tukin_pool, 0, ',', '.') }}</p>
                    <p class="text-[9px] text-slate-550 mt-1">Diberikan sesuai Grade KPI wajib</p>
                </div>
            </div>

            <!-- 7 SDM Payroll Distribution List -->
            <div class="bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Distribusi Gaji & Evaluasi KPI</h3>
                        <p class="text-[11px] text-slate-550 mt-0.5">Silakan isi Grade KPI untuk masing-masing SDM demi mengalkulasi Tukin.</p>
                    </div>
                    @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                        @php
                            $anyPending = $distributions->contains('status_pembayaran', 'pending');
                        @endphp
                        @if($anyPending)
                            <button type="button" @click="formToSubmit = 'form-pay-all'; showConfirmModal = true" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/15 cursor-pointer">
                                Kirim / Bayar Gaji (7 SDM)
                            </button>
                            <form id="form-pay-all" action="{{ route('treasury.payroll.bayar', $selectedLog->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @else
                            <span class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/10 text-emerald-400 text-xs font-bold rounded-xl">
                                Semua Gaji Terbayar
                            </span>
                        @endif
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/5 bg-slate-950/40 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-4">Nama SDM</th>
                                <th class="p-4">Role</th>
                                <th class="p-4">Nominal Gaji Pokok</th>
                                <th class="p-4">Grade KPI & Tukin (30% Pool)</th>
                                <th class="p-4">Total Diterima</th>
                                <th class="p-4">Status</th>
                                @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                                    <th class="p-4 text-center">Aksi KPI</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($distributions as $dist)
                                <tr class="text-xs hover:bg-white/5 transition-colors">
                                    <td class="p-4 font-bold text-white">{{ $dist->user->name }}</td>
                                    <td class="p-4 text-slate-400">{{ $dist->user->role ? $dist->user->role->name : 'No Role' }}</td>
                                    <td class="p-4 text-slate-300">Rp {{ number_format($dist->nominal_gapok_diterima, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        @if($dist->kpiGrade)
                                            <div class="space-y-1">
                                                <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 font-bold rounded border border-indigo-500/10 text-[10px]">
                                                    Grade {{ $dist->kpiGrade->grade_name }} ({{ $dist->kpiGrade->weight_percentage }}%)
                                                </span>
                                                <div class="text-slate-350">Rp {{ number_format($dist->nominal_tukin_diterima, 0, ',', '.') }}</div>
                                            </div>
                                        @else
                                            <span class="text-slate-550 font-semibold italic">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-white font-extrabold text-sm">
                                        Rp {{ number_format($dist->nominal_gapok_diterima + $dist->nominal_tukin_diterima, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded {{ $dist->status_pembayaran === 'paid' ? 'bg-emerald-500/10 text-emerald-450 border border-emerald-500/10' : 'bg-amber-500/10 text-amber-450 border border-amber-500/10' }}">
                                            {{ $dist->status_pembayaran }}
                                        </span>
                                    </td>
                                    @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                                        <td class="p-4">
                                            @if($dist->status_pembayaran === 'pending')
                                                <form action="{{ route('treasury.payroll.kpi', $dist->id) }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    <select name="kpi_grade_id" onchange="this.form.submit()" class="bg-slate-950 border border-white/5 rounded-lg px-2 py-1.5 text-[10px] text-white focus:outline-none focus:border-indigo-500">
                                                        <option value="">-- Pilih KPI --</option>
                                                        @foreach($kpiGrades as $kg)
                                                            <option value="{{ $kg->id }}" {{ $dist->kpi_grade_id == $kg->id ? 'selected' : '' }}>
                                                                Grade {{ $kg->grade_name }} ({{ $kg->weight_percentage }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @else
                                                <span class="text-slate-600 font-semibold">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-slate-900/20 border border-white/5 rounded-2xl p-12 text-center text-slate-550 text-sm">
                Silakan pilih log omset di atas untuk memuat data evaluasi dan payroll.
            </div>
        @endif

        <!-- Double Confirmation Modal -->
        <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm" style="display: none;">
            <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
                <div class="text-center space-y-3">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-rose-500/10 text-rose-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Konfirmasi Pembayaran Gaji</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Apakah Anda yakin ingin memproses pembayaran ini? Aksi ini akan mengubah status payroll menjadi <span class="text-emerald-400 font-bold">PAID</span> secara permanen dan otomatis mencatat pengeluaran di Buku Kas Besar.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showConfirmModal = false" class="w-full sm:w-auto px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="button" @click="document.getElementById(formToSubmit).submit(); showConfirmModal = false;" class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold cursor-pointer">Ya, Bayar Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
