<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                        </svg>
                    </span>
                    Buku Kas Besar
                </h2>
                <p class="text-xs text-slate-400 mt-1">Pencatatan Keuangan Masuk & Keluar Secara Manual dan Otomatis</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Main Stats Cashbook -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saldo Riil Saat Ini</p>
                <h3 class="text-3xl font-extrabold text-white mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
            </div>
            @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                <button onclick="document.getElementById('modal-cash').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-indigo-600/10 cursor-pointer flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Catat Transaksi Manual
                </button>
            @endif
        </div>

        <!-- Transactions Table -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Histori Transaksi Buku Kas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-slate-950/40 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4">Tipe</th>
                            <th class="p-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($transactions as $tx)
                            <tr class="text-xs hover:bg-white/5 transition-colors">
                                <td class="p-4 text-slate-300 font-semibold">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded border border-white/5 bg-white/5 text-slate-350">
                                        {{ $tx->kategori }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-200 max-w-xs truncate" title="{{ $tx->deskripsi }}">{{ $tx->deskripsi }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $tx->tipe === 'in' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/10' : 'bg-rose-500/10 text-rose-400 border border-rose-500/10' }}">
                                        {{ $tx->tipe === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right font-extrabold text-sm {{ $tx->tipe === 'in' ? 'text-emerald-400' : 'text-rose-450' }}">
                                    {{ $tx->tipe === 'in' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="p-4 border-t border-white/5 bg-slate-950/20">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form (Create Cash Transaction) -->
    <div id="modal-cash" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-white">Catat Transaksi Kas Baru</h3>
                <button onclick="document.getElementById('modal-cash').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('treasury.cashbook.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Tipe Transaksi</label>
                    <select name="tipe" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="in">Kas Masuk (In)</option>
                        <option value="out">Kas Keluar (Out)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-455 uppercase mb-2">Kategori</label>
                    <select name="kategori" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="operasional">Operasional</option>
                        <option value="event">Event</option>
                        <option value="payroll">Payroll</option>
                        <option value="omset">Omset</option>
                    </select>
                </div>

                <div x-data="{
                    displayVal: '',
                    rawVal: 0,
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
                    updateNominal(val) {
                        this.displayVal = this.formatRupiah(val);
                        let cleanNum = parseFloat(val.replace(/\./g, ''));
                        this.rawVal = isNaN(cleanNum) ? 0 : cleanNum;
                    }
                }">
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Nominal (Rp)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-xs font-bold text-slate-405">Rp</span>
                        <input type="text" 
                               required 
                               placeholder="Contoh: 500.000" 
                               x-model="displayVal"
                               @input="updateNominal($event.target.value)"
                               class="w-full bg-slate-950 border border-white/5 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <input type="hidden" name="nominal" :value="rawVal">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Deskripsi</label>
                    <textarea name="deskripsi" required placeholder="Tuliskan keterangan detail transaksi..." rows="3" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-cash').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
