<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-white leading-tight">
            Compress PDF
        </h2>
        <p class="text-sm text-slate-400 mt-1">Kurangi ukuran file PDF tanpa kehilangan konten penting.</p>
    </x-slot>

    <div class="max-w-3xl" x-data="pdfCompressor()">

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-rose-400 font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('pdf-compress.compress') }}" method="POST" enctype="multipart/form-data"
              @submit="handleSubmit">
            @csrf

            {{-- Drop Zone --}}
            <div class="bg-slate-900/60 border border-white/8 rounded-2xl overflow-hidden mb-6">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Upload File PDF
                    </h3>
                </div>

                <div class="p-6">
                    {{-- Drop Zone Area --}}
                    <div
                        class="relative border-2 border-dashed rounded-2xl transition-all duration-200 cursor-pointer"
                        :class="isDragging
                            ? 'border-indigo-500 bg-indigo-500/10'
                            : hasFile
                                ? 'border-emerald-500/50 bg-emerald-500/5'
                                : 'border-slate-700 hover:border-indigo-500/50 hover:bg-slate-800/40'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                    >
                        <input
                            type="file"
                            name="pdf"
                            accept=".pdf,application/pdf"
                            class="hidden"
                            x-ref="fileInput"
                            @change="handleFileChange($event)"
                        >

                        {{-- Empty state --}}
                        <div x-show="!hasFile" class="flex flex-col items-center justify-center py-14 px-6 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-white/5 flex items-center justify-center mb-4 shadow-xl">
                                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-300 mb-1">Seret & lepas file PDF di sini</p>
                            <p class="text-xs text-slate-500">atau klik untuk memilih file</p>
                            <p class="text-xs text-slate-600 mt-3">Format: .pdf &bull; Maks. 100MB</p>
                        </div>

                        {{-- File selected state --}}
                        <div x-show="hasFile" class="flex items-center gap-4 p-5">
                            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-rose-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z" opacity="0"/>
                                    <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V9H10c.83 0 1.5.67 1.5 1.5v.5zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V9H15c.83 0 1.5.67 1.5 1.5v1zm4-3H19v1h1.5V11H19v2h-1.5V9h3v.5zM9 9.5h1v1H9v-1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-1h-1v1z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate" x-text="fileName"></p>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="fileSize"></p>
                            </div>
                            <button type="button"
                                    @click.stop="clearFile"
                                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-500/20 border border-white/5 hover:border-rose-500/20 flex items-center justify-center text-slate-400 hover:text-rose-400 transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Compression Level --}}
            <div class="bg-slate-900/60 border border-white/8 rounded-2xl overflow-hidden mb-6">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Tingkat Kompresi
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach ($levels as $key => $level)
                            <label class="cursor-pointer">
                                <input type="radio" name="level" value="{{ $key }}" class="hidden peer"
                                    {{ $key === 'medium' ? 'checked' : '' }}>
                                <div class="p-4 rounded-xl border transition-all duration-200
                                    border-slate-700 bg-slate-800/40 text-center
                                    peer-checked:border-indigo-500/70 peer-checked:bg-indigo-500/10
                                    hover:border-indigo-500/40 hover:bg-slate-800">

                                    @php
                                        $icons = [
                                            'low'     => ['color' => 'text-emerald-400', 'bars' => 1],
                                            'medium'  => ['color' => 'text-sky-400',     'bars' => 2],
                                            'high'    => ['color' => 'text-amber-400',   'bars' => 3],
                                            'extreme' => ['color' => 'text-rose-400',    'bars' => 4],
                                        ];
                                        $icon = $icons[$key];
                                    @endphp

                                    {{-- Signal bars icon --}}
                                    <div class="flex items-end justify-center gap-0.5 mb-3 h-6">
                                        @for ($i = 1; $i <= 4; $i++)
                                            <div class="w-1.5 rounded-sm transition-all {{ $i <= $icon['bars'] ? $icon['color'] . ' bg-current' : 'bg-slate-700' }}"
                                                 style="height: {{ $i * 25 }}%"></div>
                                        @endfor
                                    </div>

                                    <p class="text-xs font-bold text-white">{{ $level['label'] }}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">
                                        @if($key === 'low') Kualitas terbaik
                                        @elseif($key === 'medium') Seimbang
                                        @elseif($key === 'high') Ukuran kecil
                                        @else Ukuran terkecil
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Description bar --}}
                    <div class="mt-4 p-3 rounded-xl bg-slate-800/60 border border-white/5">
                        <p class="text-xs text-slate-400 leading-relaxed">
                            <span class="text-indigo-400 font-semibold">Catatan:</span>
                            Semakin tinggi tingkat kompresi, semakin kecil ukuran file — namun kualitas gambar di dalam PDF akan berkurang. Untuk dokumen teks saja, semua level menghasilkan kualitas yang hampir sama.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-between">
                <div x-show="hasFile" x-transition class="text-xs text-slate-500">
                    File siap dikompres
                </div>
                <div x-show="!hasFile" class="text-xs text-slate-600">
                    Pilih file PDF untuk memulai
                </div>

                <button type="submit"
                        :disabled="!hasFile || isLoading"
                        :class="hasFile && !isLoading
                            ? 'bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white cursor-pointer shadow-lg shadow-indigo-500/20'
                            : 'bg-slate-800 text-slate-600 cursor-not-allowed'"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-200 focus:outline-none">

                    {{-- Loading spinner --}}
                    <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>

                    {{-- Compress icon --}}
                    <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>

                    <span x-text="isLoading ? 'Mengompres...' : 'Compress & Unduh'"></span>
                </button>
            </div>
        </form>

        {{-- How It Works --}}
        <div class="mt-8 bg-slate-900/40 border border-white/5 rounded-2xl p-6">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Cara Kerja</h4>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-indigo-400">1</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">Upload PDF</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Seret atau pilih file PDF hingga 100MB</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-violet-400">2</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">Pilih Level</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Tentukan keseimbangan ukuran vs kualitas</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-emerald-400">3</span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">Unduh Hasil</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">File terkompresi otomatis terunduh</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pdfCompressor() {
            return {
                hasFile: false,
                isDragging: false,
                isLoading: false,
                fileName: '',
                fileSize: '',

                handleFileChange(event) {
                    const file = event.target.files[0];
                    if (file) this.setFile(file);
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];
                    if (file && file.type === 'application/pdf') {
                        this.setFile(file);
                        // Transfer to real input
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        this.$refs.fileInput.files = dt.files;
                    }
                },

                setFile(file) {
                    this.hasFile = true;
                    this.fileName = file.name;
                    this.fileSize = this.formatBytes(file.size);
                },

                clearFile() {
                    this.hasFile = false;
                    this.fileName = '';
                    this.fileSize = '';
                    this.$refs.fileInput.value = '';
                },

                handleSubmit() {
                    if (this.hasFile) {
                        this.isLoading = true;
                    }
                },

                formatBytes(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / 1048576).toFixed(2) + ' MB';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
