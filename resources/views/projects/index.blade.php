<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-extrabold text-2xl text-white leading-tight">
                {{ __('Proyek Saya') }}
            </h2>
            <button 
                x-data="" 
                @click="$dispatch('open-modal', 'createProjectModal')"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-indigo-500/10 focus:outline-none transition-all duration-200 uppercase tracking-widest cursor-pointer"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </button>
        </div>
    </x-slot>

    <div class="space-y-8">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-950/40 border border-emerald-800/50 rounded-xl text-emerald-450 text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-450" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-slate-900/30 border border-white/5 rounded-2xl flex flex-col justify-between overflow-hidden group hover:border-indigo-500/25 transition-all duration-300 shadow-xl relative">
                    <!-- Top Accent Line -->
                    <div class="h-1 bg-{{ $project->color }}-500"></div>
                    
                    <div class="p-6 flex-grow space-y-3">
                        <div class="flex justify-between items-center gap-4">
                            <h3 class="text-base font-bold text-white group-hover:text-indigo-400 transition-colors truncate">
                                {{ $project->name }}
                            </h3>
                            <!-- Color dot indicator -->
                            <span class="w-2 h-2 rounded-full bg-{{ $project->color }}-500 shrink-0"></span>
                        </div>
                        
                        <p class="text-xs text-slate-400 leading-relaxed line-clamp-3 font-medium">
                            {{ $project->description ?: 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                    
                    <!-- Footer actions and stats -->
                    <div class="px-6 py-4.5 bg-slate-950/30 border-t border-white/5 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-550" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ $project->tasks_count }} Tugas Aktif
                        </span>

                        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center text-xs font-bold text-indigo-400 hover:text-indigo-300 gap-0.5 group/link">
                            <span>Buka Board</span>
                            <svg class="w-4 h-4 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-900/10 border-2 border-dashed border-white/5 rounded-2xl p-12 text-center flex flex-col items-center justify-center space-y-4">
                    <div class="p-4 bg-slate-900 border border-white/5 text-slate-450 rounded-2xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-200">Belum ada Proyek</h3>
                        <p class="text-xs text-slate-500 font-semibold max-w-xs mx-auto">
                            Buat proyek pertama Anda untuk mulai mengelola tugas dengan Kanban Board interaktif.
                        </p>
                    </div>
                    <button 
                        x-data="" 
                        @click="$dispatch('open-modal', 'createProjectModal')"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold rounded-xl shadow-lg transition duration-150 uppercase tracking-widest cursor-pointer"
                    >
                        Buat Proyek Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Project Modal -->
    <x-modal name="createProjectModal" :show="$errors->any()" focusable>
        <form action="{{ route('projects.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-1">
                <h3 class="text-lg font-bold text-white" id="modal-title">
                    Buat Proyek Baru
                </h3>
                <p class="text-xs text-slate-400 font-semibold">Tambahkan proyek baru ke dalam vault untuk memulai pelacakan tugas.</p>
            </div>

            <div class="space-y-4">
                <!-- Name -->
                <div class="space-y-2">
                    <x-input-label for="name" :value="__('Nama Proyek')" />
                    <x-text-input id="name" name="name" type="text" required placeholder="Contoh: Desain Landing Page" />
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <x-input-label for="description" :value="__('Deskripsi')" />
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="3" 
                        class="block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-250 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm" 
                        placeholder="Detail singkat mengenai proyek..."
                    ></textarea>
                </div>

                <!-- Color Accent selector -->
                <div class="space-y-2">
                    <x-input-label :value="__('Warna Aksen Proyek')" />
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

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button x-data="" @click="$dispatch('close-modal', 'createProjectModal')">
                    Batal
                </x-secondary-button>

                <x-primary-button>
                    Simpan Proyek
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
