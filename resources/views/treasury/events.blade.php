<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </span>
                    Pengeluaran Event & Akomodasi
                </h2>
                <p class="text-xs text-slate-400 mt-1">Alokasikan Rencana Anggaran (Budget) & Catat Rincian Realisasi Pengeluaran per Item + Kuantitas</p>
            </div>
            
            @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                <button onclick="document.getElementById('modal-budget').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10 cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Rencana Budget
                </button>
            @endif
        </div>
    </x-slot>

    <!-- Main Container (Responsive: 2-Columns Cards for Mobile and Desktop views) -->
    <div class="grid grid-cols-2 gap-6">
        @forelse($events as $event)
            <div class="bg-slate-900/40 border border-white/5 rounded-2xl p-6 flex flex-col justify-between space-y-6">
                <div>
                    <!-- Header Event Card -->
                    <div class="flex justify-between items-start border-b border-white/5 pb-4 gap-4">
                        <div>
                            <span class="text-[9px] font-bold text-slate-500 uppercase">PIC: {{ $event->pic->name }}</span>
                            <h3 class="text-base font-bold text-white mt-0.5">{{ $event->nama_event }}</h3>
                            <p class="text-[10px] text-slate-400">{{ $event->tanggal_event ? $event->tanggal_event->format('d M Y') : $event->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[9px] font-bold text-slate-550 uppercase block">Total Plafon</span>
                            <span class="text-sm font-extrabold text-indigo-400">Rp {{ number_format($event->total_budget, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- 3 Category Plafons & Real Expenses summary -->
                    <div class="grid grid-cols-3 gap-2 text-[10px] mt-4">
                        <div class="bg-slate-950/40 p-2 rounded-xl border border-white/5">
                            <span class="font-bold text-slate-400 block text-[8px] uppercase tracking-wider">Transport</span>
                            <p class="text-slate-200 mt-0.5 font-bold">Rp {{ number_format($event->budget_transportasi, 0, ',', '.') }}</p>
                            <span class="text-[9px] text-slate-550 block mt-0.5">Real: Rp {{ number_format($event->expenses->where('kategori', 'transportasi')->sum('total_harga'), 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-950/40 p-2 rounded-xl border border-white/5">
                            <span class="font-bold text-slate-400 block text-[8px] uppercase tracking-wider">Akomodasi</span>
                            <p class="text-slate-200 mt-0.5 font-bold">Rp {{ number_format($event->budget_akomodasi, 0, ',', '.') }}</p>
                            <span class="text-[9px] text-slate-550 block mt-0.5">Real: Rp {{ number_format($event->expenses->where('kategori', 'akomodasi')->sum('total_harga'), 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-950/40 p-2 rounded-xl border border-white/5">
                            <span class="font-bold text-slate-400 block text-[8px] uppercase tracking-wider">Venue</span>
                            <p class="text-slate-200 mt-0.5 font-bold">Rp {{ number_format($event->budget_venue, 0, ',', '.') }}</p>
                            <span class="text-[9px] text-slate-550 block mt-0.5">Real: Rp {{ number_format($event->expenses->where('kategori', 'venue')->sum('total_harga'), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Real Expenses (Pencatatan Rincian Sebenarnya) -->
                    <div class="space-y-3 pt-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-[10px] font-bold text-white uppercase tracking-wider">Rincian Pengeluaran Riel</h4>
                            @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                                <button onclick="document.getElementById('modal-expense-{{ $event->id }}').classList.remove('hidden')" class="px-2.5 py-1.5 bg-indigo-650 hover:bg-indigo-600 text-white rounded-lg text-[9px] font-bold cursor-pointer">
                                    + Catat Item Riil
                                </button>
                            @endif
                        </div>

                        <!-- Real Expenses Table (Mini list) -->
                        <div class="border border-white/5 rounded-xl bg-slate-950/30 overflow-hidden text-[10px]">
                            <div class="divide-y divide-white/5">
                                @forelse($event->expenses as $exp)
                                    <div class="p-3 flex justify-between items-center hover:bg-white/5">
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-white">{{ $exp->nama_item }} (Qty: {{ $exp->quantity }})</div>
                                            <div class="text-[9px] text-slate-500">
                                                {{ $exp->tanggal_pengeluaran ? $exp->tanggal_pengeluaran->format('d M Y') : $exp->created_at->format('d M Y') }} &bull; <span class="uppercase text-slate-400 font-bold">{{ $exp->kategori }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-white">Rp {{ number_format($exp->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-550 italic">Belum ada pengeluaran riil per item.</div>
                                @endforelse
                                @if($event->expenses->count() > 0)
                                    <div class="p-3 bg-slate-950/60 font-bold flex justify-between items-center text-slate-400 border-t border-white/10">
                                        <span class="uppercase text-[9px]">Total Realisasi:</span>
                                        <span class="text-xs text-indigo-400 font-extrabold">Rp {{ number_format($event->expenses->sum('total_harga'), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Catat Pengeluaran Riil (Per Event) -->
                @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                    <div id="modal-expense-{{ $event->id }}" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
                        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-base font-bold text-white">Catat Pengeluaran Riil</h3>
                                    <p class="text-[10px] text-slate-400 mt-1">Event: {{ $event->nama_event }}</p>
                                </div>
                                <button onclick="document.getElementById('modal-expense-{{ $event->id }}').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('treasury.events.expense.store', $event->id) }}" method="POST" class="space-y-4" x-data="{
                                qty: 1,
                                price: 0,
                                get subtotal() { return this.qty * this.price },
                                
                                displayPrice: '',
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
                                updatePrice(val) {
                                    this.displayPrice = this.formatRupiah(val);
                                    let cleanNum = parseFloat(val.replace(/\./g, ''));
                                    this.price = isNaN(cleanNum) ? 0 : cleanNum;
                                }
                            }">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Tanggal Pengeluaran</label>
                                    <input type="date" name="tanggal_pengeluaran" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Kategori Pengeluaran</label>
                                    <select name="kategori" required class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                        <option value="transportasi">Transportasi</option>
                                        <option value="akomodasi">Akomodasi</option>
                                        <option value="venue">Venue / Lapangan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Nama Item Pengeluaran</label>
                                    <input type="text" name="nama_item" required placeholder="Contoh: Tiket Pesawat Citilink" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Kuantitas (Qty)</label>
                                        <input type="number" name="quantity" required min="1" x-model.number="qty" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Harga Satuan</label>
                                        <div class="relative flex items-center">
                                            <span class="absolute left-3 text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" 
                                                   required 
                                                   placeholder="Contoh: 600.000" 
                                                   x-model="displayPrice"
                                                   @input="updatePrice($event.target.value)"
                                                   class="w-full bg-slate-950 border border-white/5 rounded-xl pl-9 pr-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                                        </div>
                                        <input type="hidden" name="harga_satuan" :value="price">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Catatan Tambahan (Opsional)</label>
                                    <input type="text" name="catatan" placeholder="Catatan opsional..." class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                </div>

                                <!-- Live Subtotal Preview -->
                                <div class="bg-indigo-950/20 border border-indigo-500/10 rounded-xl p-4 flex justify-between items-center text-xs">
                                    <span class="font-bold text-indigo-400 uppercase tracking-wider text-[10px]">Total Sub Pengeluaran:</span>
                                    <span class="text-base font-extrabold text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(subtotal)"></span></span>
                                </div>

                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" onclick="document.getElementById('modal-expense-{{ $event->id }}').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                                    <button type="submit" class="px-4 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan & Debit Kas</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-2 text-center py-20 bg-slate-900/20 border border-dashed border-white/5 rounded-2xl text-slate-500 text-xs">
                <svg class="w-12 h-12 mx-auto text-slate-650 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Belum ada rencana budget event aktif yang didaftarkan.
            </div>
        @endforelse
    </div>

    <!-- Modal Form (Tambah Budget Event Baru) -->
    @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
        <div id="modal-budget" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
            <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-bold text-white">Rencana Budget Event</h3>
                    <button onclick="document.getElementById('modal-budget').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('treasury.events.store') }}" method="POST" class="space-y-4" x-data="{
                    trans: 0,
                    akom: 0,
                    venue: 0,
                    get total() { return this.trans + this.akom + this.venue },
                    
                    displayTrans: '',
                    displayAkom: '',
                    displayVenue: '',
                    
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
                    updateTrans(val) {
                        this.displayTrans = this.formatRupiah(val);
                        let cleanNum = parseFloat(val.replace(/\./g, ''));
                        this.trans = isNaN(cleanNum) ? 0 : cleanNum;
                    },
                    updateAkom(val) {
                        this.displayAkom = this.formatRupiah(val);
                        let cleanNum = parseFloat(val.replace(/\./g, ''));
                        this.akom = isNaN(cleanNum) ? 0 : cleanNum;
                    },
                    updateVenue(val) {
                        this.displayVenue = this.formatRupiah(val);
                        let cleanNum = parseFloat(val.replace(/\./g, ''));
                        this.venue = isNaN(cleanNum) ? 0 : cleanNum;
                    }
                }">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Nama Event / Acara</label>
                        <input type="text" name="nama_event" required placeholder="Contoh: Expo Nasional 2026" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_event" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-450 uppercase mb-2">PIC Event (SDM)</label>
                        <select name="pic_id" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ? $user->role->name : 'No Role' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Plafon 3 Kategori -->
                    <div class="space-y-3 pt-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 uppercase mb-1">Budget Transportasi</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" 
                                       required 
                                       placeholder="Contoh: 5.000.000" 
                                       x-model="displayTrans"
                                       @input="updateTrans($event.target.value)"
                                       class="w-full bg-slate-950 border border-white/5 rounded-xl pl-10 pr-4 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            </div>
                            <input type="hidden" name="budget_transportasi" :value="trans">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 uppercase mb-1">Budget Akomodasi</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" 
                                       required 
                                       placeholder="Contoh: 8.000.000" 
                                       x-model="displayAkom"
                                       @input="updateAkom($event.target.value)"
                                       class="w-full bg-slate-950 border border-white/5 rounded-xl pl-10 pr-4 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            </div>
                            <input type="hidden" name="budget_akomodasi" :value="akom">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 uppercase mb-1">Budget Venue</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" 
                                       required 
                                       placeholder="Contoh: 12.000.000" 
                                       x-model="displayVenue"
                                       @input="updateVenue($event.target.value)"
                                       class="w-full bg-slate-950 border border-white/5 rounded-xl pl-10 pr-4 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            </div>
                            <input type="hidden" name="budget_venue" :value="venue">
                        </div>
                    </div>

                    <!-- Live Total Preview -->
                    <div class="bg-indigo-950/20 border border-indigo-500/10 rounded-xl p-4 flex justify-between items-center text-xs">
                        <span class="font-bold text-indigo-400 uppercase tracking-wider text-[10px]">Total Plafon:</span>
                        <span class="text-base font-extrabold text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(total)"></span></span>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('modal-budget').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan Rencana Plafon</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
