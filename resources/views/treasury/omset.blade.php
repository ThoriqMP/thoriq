<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Input Omset & Usulan Sales
                </h2>
                <p class="text-xs text-slate-400 mt-1">Form Pengajuan Omset Masuk dan Validasi Alokasi Persentase Otomatis</p>
            </div>
            
            @if(Auth::user()->hasRole(['Treasury', 'Sales']))
                <button onclick="document.getElementById('modal-omset').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10 cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Catat Omset Baru
                </button>
            @endif
        </div>
    </x-slot>

    <!-- Main Container -->
    <div class="space-y-6">
        
        <!-- List Omset Logs (Responsive: Desktop Table vs. Mobile 2-Column Cards) -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Histori Omset Logs</h3>
            </div>

            <!-- Desktop View: Table (Hidden on Mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-slate-950/40 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Omset (A)</th>
                            <th class="p-4">Pembagian B / C</th>
                            <th class="p-4">Sales PIC</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($omsetLogs as $log)
                            <tr class="text-xs hover:bg-white/5 transition-colors">
                                <td class="p-4 text-slate-300 font-semibold">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                <td class="p-4 text-white font-extrabold">Rp {{ number_format($log->nominal_omset, 0, ',', '.') }}</td>
                                <td class="p-4 text-slate-450 space-y-1">
                                    <div>Gaji: Rp {{ number_format($log->alokasi_gaji, 0, ',', '.') }}</div>
                                    <div>Kas: Rp {{ number_format($log->alokasi_perusahaan, 0, ',', '.') }}</div>
                                </td>
                                <td class="p-4 text-slate-350">{{ $log->sales->name }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded {{ $log->status === 'approved' ? 'bg-emerald-500/10 text-emerald-450 border border-emerald-500/10' : 'bg-amber-500/10 text-amber-450 border border-amber-500/10' }}">
                                        {{ $log->status === 'approved' ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center flex items-center justify-center gap-2">
                                    @if($log->status === 'pending' && Auth::user()->hasRole(['Treasury', 'Head']))
                                        <form action="{{ route('treasury.omset.approve', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-555 text-white text-[10px] font-bold rounded-lg transition cursor-pointer">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if(Auth::user()->hasRole(['Treasury', 'Head']))
                                        <button type="button" onclick="confirmDeleteOmset('{{ route('treasury.omset.destroy', $log->id) }}')" class="p-1.5 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition cursor-pointer" title="Hapus Omset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">Belum ada pengajuan omset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: 2-Column Grid (Hidden on Desktop) -->
            <div class="block md:hidden p-4">
                <div class="grid grid-cols-2 gap-4">
                    @forelse($omsetLogs as $log)
                        <div class="bg-slate-950/60 border border-white/5 rounded-xl p-4 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[9px] text-slate-550 font-bold">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</span>
                                    <span class="px-1.5 py-0.5 text-[8px] font-bold uppercase rounded {{ $log->status === 'approved' ? 'bg-emerald-500/10 text-emerald-450' : 'bg-amber-500/10 text-amber-450' }}">
                                        {{ $log->status === 'approved' ? 'App' : 'Pend' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-extrabold text-white">Rp {{ number_format($log->nominal_omset, 0, ',', '.') }}</h4>
                                
                                <div class="text-[10px] text-slate-450 space-y-0.5 mt-2 pt-2 border-t border-white/5">
                                    <div>Gaji: Rp {{ number_format($log->alokasi_gaji, 0, ',', '.') }}</div>
                                    <div>Kas: Rp {{ number_format($log->alokasi_perusahaan, 0, ',', '.') }}</div>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1 truncate">PIC: {{ $log->sales->name }}</div>
                            </div>

                            <div class="flex gap-2 w-full">
                                @if($log->status === 'pending' && Auth::user()->hasRole(['Treasury', 'Head']))
                                    <form action="{{ route('treasury.omset.approve', $log->id) }}" method="POST" class="flex-grow">
                                        @csrf
                                        <button type="submit" class="w-full text-center py-1.5 bg-emerald-600 hover:bg-emerald-555 text-white text-[10px] font-bold rounded-lg transition cursor-pointer">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                @if(Auth::user()->hasRole(['Treasury', 'Head']))
                                    <button type="button" onclick="confirmDeleteOmset('{{ route('treasury.omset.destroy', $log->id) }}')" class="px-2 py-1.5 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition cursor-pointer flex items-center justify-center shrink-0" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-12 text-slate-500 text-xs">Belum ada pengajuan omset.</div>
                    @endforelse
                </div>
            </div>

            @if($omsetLogs->hasPages())
                <div class="p-4 border-t border-white/5 bg-slate-950/20">
                    {{ $omsetLogs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form (Tambah Omset Baru) -->
    <div id="modal-omset" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-white">Catat Usulan Omset</h3>
                <button onclick="document.getElementById('modal-omset').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('treasury.omset.store') }}" method="POST" class="space-y-4" x-data="{ 
                omset: 0, 
                get alokasiGaji() { return this.omset * 0.70 },
                get alokasiPerusahaan() { return this.omset * 0.30 },
                get gapokPool() { return this.alokasiGaji * 0.70 },
                get tukinPool() { return this.alokasiGaji * 0.30 }
            }">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div x-data="{
                    displayVal: '',
                    formatRupiah(val) {
                        let number_string = val.replace(/[^,\d]/g, '').toString(),
                            split = number_string.split(','),
                            sisa = split[0].length % 3,
                            rupiah = split[0].substr(0, sisa),
                            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                        if (ribuan) {
                            let separator = sisa ? '.' : '';
                            rupiah += separator + ribuan.join('.');
                        }
                        return rupiah;
                    },
                    updateOmset(val) {
                        this.displayVal = this.formatRupiah(val);
                        let cleanNum = parseFloat(val.replace(/\./g, ''));
                        omset = isNaN(cleanNum) ? 0 : cleanNum;
                    }
                }">
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Nominal Omset (A)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-xs font-bold text-slate-400">Rp</span>
                        <input type="text" 
                               required 
                               placeholder="Contoh: 100.000.000" 
                               x-model="displayVal"
                               @input="updateOmset($event.target.value)"
                               class="w-full bg-slate-950 border border-white/5 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <input type="hidden" name="nominal_omset" :value="omset">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Marketing PIC</label>
                    <select name="sales_id" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($marketingUsers as $su)
                            <option value="{{ $su->id }}" {{ $su->id === Auth::id() ? 'selected' : '' }}>{{ $su->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Live Calculation Box -->
                <div x-show="omset > 0" class="bg-indigo-950/20 border border-indigo-500/10 rounded-xl p-4 space-y-2 text-xs" style="display: none;">
                    <h4 class="font-bold text-indigo-400 uppercase tracking-wider mb-2 text-[10px]">Live Preview Alokasi</h4>
                    <div class="flex justify-between text-slate-300">
                        <span>Anggaran Gaji (B - 70%):</span>
                        <span class="font-bold text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(alokasiGaji)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Kas Perusahaan (C - 30%):</span>
                        <span class="font-bold text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(alokasiPerusahaan)"></span></span>
                    </div>
                    <div class="border-t border-white/5 my-2 pt-2 flex justify-between text-slate-400">
                        <span>Gaji Pokok Pool (70% B):</span>
                        <span>Rp <span x-text="new Intl.NumberFormat('id-ID').format(gapokPool)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Tukin Pool (30% B):</span>
                        <span>Rp <span x-text="new Intl.NumberFormat('id-ID').format(tukinPool)"></span></span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-omset').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold cursor-pointer">
                        {{ (Auth::user()->role && Auth::user()->role->name === 'Treasury') ? 'Simpan & Auto-Setujui' : 'Kirim Usulan Omset' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Konfirmasi Hapus Omset Global -->
    <div id="modal-delete-confirm" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-sm p-6 space-y-6">
            <div class="text-center space-y-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-500/10 text-rose-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">Konfirmasi Hapus Omset</h3>
                <p class="text-xs text-slate-400">Apakah Anda yakin ingin menghapus data omset ini? Tindakan ini akan menghapus alokasi kas masuk dan seluruh data payroll terkait secara permanen.</p>
            </div>
            
            <form id="form-delete-confirm" method="POST" class="flex gap-3 justify-center">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete-confirm').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold cursor-pointer">Ya, Hapus</button>
            </form>
        </div>
    </div>
    
    <script>
        function confirmDeleteOmset(actionUrl) {
            const form = document.getElementById('form-delete-confirm');
            form.action = actionUrl;
            document.getElementById('modal-delete-confirm').classList.remove('hidden');
        }
    </script>
</x-app-layout>
