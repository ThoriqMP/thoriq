<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-extrabold text-2xl text-white leading-tight">
                {{ __('Arsip Dokumen & Berkas') }}
            </h2>
            <button 
                onclick="toggleModal('createTagModal')" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-white/5 hover:bg-white/10 text-indigo-400 hover:text-indigo-300 text-xs font-bold rounded-xl border border-white/5 transition duration-150 uppercase tracking-widest cursor-pointer"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l4.318-4.318a1.125 1.125 0 000-1.591l-9.581-9.581a1.125 1.125 0 00-.786-.33z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01" />
                </svg>
                Buat Tag Baru
            </button>
        </div>
    </x-slot>

    <div class="space-y-8">
        
        <!-- Success Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-950/40 border border-emerald-800/50 rounded-xl text-emerald-455 text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Validation Error Alert (For identifying upload failures like files exceeding 20MB) -->
        @if($errors->any())
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold rounded-xl space-y-2">
                <div class="flex items-center gap-2 font-bold">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Gagal mengunggah berkas:</span>
                </div>
                <ul class="list-disc list-inside text-xs font-semibold pl-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Split Layout (Sidebar Form & Library Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left Column: Upload Panel -->
            <div class="bg-slate-900/30 border border-white/5 rounded-2xl p-6 space-y-6 shadow-xl">
                <h3 class="font-bold text-base text-white">Unggah Berkas Baru</h3>
                
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Drag and Drop Dropzone -->
                    <div id="dropzone" class="border-2 border-dashed border-slate-800 hover:border-indigo-500/50 rounded-2xl p-6 text-center cursor-pointer hover:bg-white/[0.01] transition-all flex flex-col justify-center items-center group relative">
                        <input type="file" name="file" id="fileInput" class="hidden" required onchange="handleFileSelect(this)">
                        
                        <svg class="w-10 h-10 text-slate-550 group-hover:text-indigo-400 group-hover:scale-105 transition duration-200 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-xs.5 font-bold text-slate-300">Tarik berkas Anda ke sini</p>
                        <p class="text-[10px] text-slate-500 font-semibold mt-1">atau klik untuk telusuri berkas (Maks 100MB)</p>
                        
                        <!-- Selected File Banner -->
                        <div id="fileDetails" class="hidden mt-4 p-3 bg-slate-950/80 rounded-xl text-left border border-white/5 w-full z-10 relative">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <svg class="w-7 h-7 text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="min-w-0">
                                    <p id="fileName" class="text-xs font-bold text-slate-200 truncate"></p>
                                    <p id="fileSize" class="text-[10px] text-slate-500 font-bold mt-0.5"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form details -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <x-input-label for="title" :value="__('Nama Dokumen')" />
                            <x-text-input type="text" name="title" id="title" placeholder="Nama berkas di sistem (opsional)" />
                        </div>
                        <div class="space-y-2">
                            <x-input-label for="description" :value="__('Deskripsi / Catatan')" />
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="3" 
                                class="block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm" 
                                placeholder="Tulis catatan opsional mengenai berkas ini..."
                            ></textarea>
                        </div>
                        
                        <!-- Tags selection -->
                        <div class="space-y-2">
                            <x-input-label :value="__('Sematkan Tag')" />
                            <div id="tags-selector-list" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-1">
                                @foreach($tags as $tag)
                                    <label class="inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" class="sr-only peer">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-950/80 text-slate-400 border border-white/5 peer-checked:bg-{{ $tag->color }}-500/10 peer-checked:text-{{ $tag->color }}-400 peer-checked:border-{{ $tag->color }}-500/20 transition">
                                            {{ $tag->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <x-primary-button class="w-full gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Unggah Dokumen</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Document Library Grid & Filters -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Search and Tag filter header -->
                <div class="bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl p-6 space-y-4">
                    <form action="{{ route('documents.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                        <div class="relative flex-grow">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm" 
                                placeholder="Cari berdasarkan nama atau deskripsi..."
                            >
                        </div>
                        
                        <div class="flex gap-2 shrink-0">
                            @if($search || $tagFilter)
                                <x-secondary-button href="{{ route('documents.index') }}" class="py-2.5">
                                    Reset
                                </x-secondary-button>
                            @endif
                            <x-primary-button class="py-2.5 px-6">
                                Cari
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Tag Pills Filters -->
                    <div class="flex flex-wrap gap-2 pt-2 items-center text-xs">
                        <span class="font-bold text-slate-500 mr-1.5">Filter Tag:</span>
                        <a href="{{ route('documents.index', ['search' => $search]) }}" class="px-3 py-1 text-[11px] font-bold rounded-full transition {{ !$tagFilter ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-950/80 text-slate-400 hover:text-white border border-white/5' }}">
                            Semua
                        </a>
                        @foreach($tags as $tag)
                            <a href="{{ route('documents.index', ['search' => $search, 'tag' => $tag->id]) }}" class="px-3 py-1 text-[11px] font-bold rounded-full border border-transparent transition
                                {{ $tagFilter == $tag->id 
                                    ? "bg-{$tag->color}-600 text-white shadow-md" 
                                    : "bg-{$tag->color}-500/10 text-{$tag->color}-400 border-{$tag->color}-500/20 hover:bg-{$tag->color}-500/20" }}">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Library Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($documents as $doc)
                        <div class="bg-slate-900/30 rounded-2xl shadow-xl border border-white/5 p-5 flex flex-col justify-between hover:border-indigo-500/20 transition-all duration-300 relative">
                            
                            <div>
                                <!-- File Icon and Actions Header -->
                                <div class="flex justify-between items-start">
                                    <!-- File Type Indicator Icon -->
                                    <div class="p-3 bg-slate-950 border border-white/5 rounded-xl">
                                        @if(str_contains($doc->mime_type, 'pdf'))
                                            <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        @elseif(str_contains($doc->mime_type, 'image'))
                                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Download & Delete Actions -->
                                    <div class="flex space-x-1">
                                        <a href="{{ route('documents.download', $doc) }}" class="p-2 hover:bg-white/5 text-slate-450 hover:text-white rounded-xl transition" title="Unduh File">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 hover:bg-rose-500/10 text-slate-450 hover:text-rose-400 rounded-xl transition cursor-pointer" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="mt-4 space-y-1">
                                    <h4 class="font-bold text-slate-200 text-base line-clamp-1" title="{{ $doc->title }}">
                                        {{ $doc->title }}
                                    </h4>
                                    <p class="text-[11px] text-slate-500 font-semibold">
                                        Ukuran: {{ round($doc->file_size / 1024, 1) }} KB &bull; Diunggah {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                    @if($doc->description)
                                        <p class="text-xs text-slate-400 mt-2.5 line-clamp-2 leading-relaxed font-medium">
                                            {{ $doc->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Tags list footer -->
                            <div class="mt-5 pt-3.5 border-t border-white/5 flex flex-wrap gap-1.5 items-center">
                                @forelse($doc->tags as $tag)
                                    <span class="px-2.5 py-0.5 text-[9px] font-extrabold rounded-full bg-{{ $tag->color }}-500/10 text-{{ $tag->color }}-400 border border-{{ $tag->color }}-500/20">
                                        {{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-slate-600 font-medium italic">Tidak ada tag</span>
                                @endforelse
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full bg-slate-900/10 border-2 border-dashed border-white/5 rounded-2xl p-12 text-center flex flex-col items-center justify-center space-y-4">
                            <div class="p-4 bg-slate-900 border border-white/5 text-slate-450 rounded-2xl">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-bold text-slate-200">Tidak ada dokumen</h3>
                                <p class="text-xs text-slate-500 font-semibold max-w-xs mx-auto">
                                    Unggah berkas baru menggunakan panel kiri untuk mengarsipkannya ke dalam vault.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Create Tag Modal (Minimalist Custom overlay) -->
    <div id="createTagModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Modal backdrop -->
            <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="toggleModal('createTagModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content Panel -->
            <div class="inline-block align-bottom bg-slate-900 border border-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form id="createTagForm" onsubmit="submitNewTag(event)">
                    @csrf
                    <div class="p-6 space-y-6">
                        <h3 class="text-lg font-bold text-white" id="modal-title">
                            Buat Tag Baru
                        </h3>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <x-input-label for="tag_name" :value="__('Nama Tag')" />
                                <x-text-input type="text" name="name" id="tag_name" required placeholder="Contoh: Keuangan, Desain, Draft" />
                            </div>
                            <div class="space-y-2">
                                <x-input-label :value="__('Pilih Warna Tag')" />
                                <div class="flex items-center space-x-3 pt-1">
                                    @foreach(['indigo', 'emerald', 'rose', 'violet', 'amber', 'sky'] as $color)
                                        <label class="relative flex items-center justify-center cursor-pointer">
                                            <input type="radio" name="color" value="{{ $color }}" {{ $color === 'indigo' ? 'checked' : '' }} class="sr-only peer">
                                            <span class="w-7 h-7 rounded-full bg-{{ $color }}-500 ring-offset-2 ring-offset-slate-950 peer-checked:ring-2 peer-checked:ring-indigo-500"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Buttons -->
                    <div class="px-6 py-4 bg-slate-950/40 border-t border-white/5 flex flex-row-reverse gap-3">
                        <x-primary-button>
                            Simpan Tag
                        </x-primary-button>
                        <x-secondary-button onclick="toggleModal('createTagModal')">
                            Batal
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Zone and Tag Creation Logic -->
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileDetails = document.getElementById('fileDetails');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const docTitleInput = document.getElementById('title');

        // Dragover
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-indigo-500', 'bg-white/[0.02]', 'scale-[1.01]');
        });

        // Dragleave
        ['dragleave', 'dragend'].forEach(type => {
            dropzone.addEventListener(type, () => {
                dropzone.classList.remove('border-indigo-500', 'bg-white/[0.02]', 'scale-[1.01]');
            });
        });

        // Drop
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-indigo-500', 'bg-white/[0.02]', 'scale-[1.01]');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect(fileInput);
            }
        });

        // Click zone
        dropzone.addEventListener('click', (e) => {
            // Avoid triggering select dialog when clicking links inside select banner details
            if (e.target.closest('#fileDetails')) return;
            fileInput.click();
        });

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                fileDetails.classList.remove('hidden');

                // Auto-fill Title input with name (without extension)
                if (!docTitleInput.value) {
                    const dotIndex = file.name.lastIndexOf('.');
                    docTitleInput.value = dotIndex !== -1 ? file.name.substring(0, dotIndex) : file.name;
                }
            }
        }

        function toggleModal(modalId) {
            document.getElementById(modalId).classList.toggle('hidden');
        }

        async function submitNewTag(e) {
            e.preventDefault();
            const form = document.getElementById('createTagForm');
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('tags.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    // Prepend new Tag to the checklist selector
                    const list = document.getElementById('tags-selector-list');
                    const newLabel = document.createElement('label');
                    newLabel.className = 'inline-flex items-center cursor-pointer select-none';
                    newLabel.innerHTML = `
                        <input type="checkbox" name="tag_ids[]" value="${data.tag.id}" class="sr-only peer">
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-950/80 text-slate-400 border border-white/5 peer-checked:bg-${data.tag.color}-500/10 peer-checked:text-${data.tag.color}-400 peer-checked:border-${data.tag.color}-500/20 transition">
                            ${data.tag.name}
                        </span>
                    `;
                    list.appendChild(newLabel);

                    toggleModal('createTagModal');
                    form.reset();
                } else {
                    alert(data.message || 'Gagal menyimpan tag.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan tag.');
            }
        }
    </script>
</x-app-layout>
