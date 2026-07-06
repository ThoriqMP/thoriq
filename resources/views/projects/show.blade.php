<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-{{ $project->color }}-500 shrink-0"></span>
                    <h2 class="font-extrabold text-2xl text-white leading-tight">
                        {{ $project->name }}
                    </h2>
                </div>
                <p class="text-xs text-slate-400 font-semibold mt-1">{{ $project->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>
            
            <div class="flex flex-wrap gap-2.5 items-center">
                <!-- Project Switcher -->
                <select onchange="window.location.href=this.value" class="rounded-xl border border-white/5 bg-slate-900/60 text-xs font-semibold text-slate-200 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-3 pr-8">
                    @foreach($projects as $p)
                        <option value="{{ route('projects.show', $p) }}" {{ $p->id === $project->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>

                <button 
                    onclick="openAddTaskModal()" 
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold rounded-xl shadow-lg hover:shadow-indigo-500/10 focus:outline-none transition-all duration-200 uppercase tracking-widest cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Tugas
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Alert Banner -->
        <div id="dragNotification" class="hidden fixed bottom-5 right-5 z-50 p-4 rounded-xl shadow-lg border text-sm font-semibold transition-all duration-300 transform translate-y-10 opacity-0 bg-slate-900 border-indigo-500/20 text-indigo-400"></div>

        <!-- Kanban Board (Fluid 3 columns) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            
            <!-- Column: To Do -->
            <div class="bg-slate-900/30 rounded-2xl p-4.5 border border-white/5 shadow-xl">
                <div class="flex justify-between items-center mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="font-bold text-xs.5 text-slate-300 uppercase tracking-wider">Belum Mulai (To Do)</h3>
                    </div>
                    <span class="text-[10px] bg-slate-950/80 border border-white/5 px-2.5 py-0.5 rounded-full font-bold text-slate-400" id="todo-count">{{ $todoTasks->count() }}</span>
                </div>

                <div id="todo-list" data-status="todo" class="task-column space-y-3.5 min-h-[450px] transition-all pb-8 rounded-xl border-2 border-transparent">
                    @foreach($todoTasks as $task)
                        @include('projects.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>

            <!-- Column: In Progress -->
            <div class="bg-slate-900/30 rounded-2xl p-4.5 border border-white/5 shadow-xl">
                <div class="flex justify-between items-center mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <h3 class="font-bold text-xs.5 text-slate-300 uppercase tracking-wider">Sedang Dikerjakan</h3>
                    </div>
                    <span class="text-[10px] bg-slate-950/80 border border-white/5 px-2.5 py-0.5 rounded-full font-bold text-slate-400" id="in_progress-count">{{ $inProgressTasks->count() }}</span>
                </div>

                <div id="in_progress-list" data-status="in_progress" class="task-column space-y-3.5 min-h-[450px] transition-all pb-8 rounded-xl border-2 border-transparent">
                    @foreach($inProgressTasks as $task)
                        @include('projects.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>

            <!-- Column: Done -->
            <div class="bg-slate-900/30 rounded-2xl p-4.5 border border-white/5 shadow-xl">
                <div class="flex justify-between items-center mb-4 px-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h3 class="font-bold text-xs.5 text-slate-300 uppercase tracking-wider">Selesai</h3>
                    </div>
                    <span class="text-[10px] bg-slate-950/80 border border-white/5 px-2.5 py-0.5 rounded-full font-bold text-slate-400" id="done-count">{{ $doneTasks->count() }}</span>
                </div>

                <div id="done-list" data-status="done" class="task-column space-y-3.5 min-h-[450px] transition-all pb-8 rounded-xl border-2 border-transparent">
                    @foreach($doneTasks as $task)
                        @include('projects.partials.task_card', ['task' => $task])
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- Task Action Modal (Create & Edit) -->
    <div id="taskModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Modal Backdrop -->
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeTaskModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel Container -->
            <div class="inline-block align-bottom bg-slate-900 border border-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Create/Update Form -->
                <form id="taskForm" action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="task_id" id="task_id" value="">
                    
                    <div class="p-6 space-y-6">
                        <div class="flex justify-between items-center pb-3 border-b border-white/5">
                            <h3 class="text-lg font-bold text-white" id="modalTitle">
                                Tambah Tugas Baru
                            </h3>
                            <button type="button" onclick="closeTaskModal()" class="text-slate-400 hover:text-white transition-colors cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Title -->
                            <div class="space-y-2">
                                <x-input-label for="task_title" :value="__('Judul Tugas')" />
                                <x-text-input id="task_title" name="title" type="text" required placeholder="Contoh: Selesaikan integrasi API" />
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <x-input-label for="task_description" :value="__('Deskripsi')" />
                                <textarea 
                                    name="description" 
                                    id="task_description" 
                                    rows="3" 
                                    class="block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm" 
                                    placeholder="Tulis rincian tugas..."
                                ></textarea>
                            </div>

                            <!-- Priority & Due Date -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <x-input-label for="task_priority" :value="__('Prioritas')" />
                                    <select name="priority" id="task_priority" class="block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm">
                                        <option value="low">Low (Rendah)</option>
                                        <option value="medium" selected>Medium (Sedang)</option>
                                        <option value="high">High (Tinggi)</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <x-input-label for="task_due_date" :value="__('Tenggat Waktu')" />
                                    <input type="date" name="due_date" id="task_due_date" class="block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm text-sm">
                                </div>
                            </div>

                            <!-- Attachments Selection -->
                            <div class="space-y-2">
                                <x-input-label :value="__('Lampirkan Berkas Kerja')" />
                                <div class="max-h-40 overflow-y-auto border border-white/5 rounded-xl p-3 bg-slate-950/40 space-y-2">
                                    @forelse($documents as $doc)
                                        <label class="flex items-center space-x-2.5 cursor-pointer p-2 hover:bg-white/5 rounded-lg transition select-none">
                                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" id="doc-checkbox-{{ $doc->id }}" class="doc-checkbox rounded bg-slate-900 border-white/5 text-indigo-600 focus:ring-indigo-500/20 focus:ring-offset-slate-900">
                                            <span class="text-xs font-semibold text-slate-300 line-clamp-1 flex-1">{{ $doc->title }}</span>
                                            <span class="text-[10px] text-slate-500 font-bold shrink-0">{{ round($doc->file_size / 1024, 1) }} KB</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-slate-500 text-center py-4">Belum ada dokumen di arsip. Unggah berkas di menu Arsip Dokumen terlebih dahulu.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Buttons -->
                    <div class="px-6 py-4 bg-slate-950/40 border-t border-white/5 flex flex-row-reverse justify-between items-center">
                        <div class="flex gap-2">
                            <x-secondary-button onclick="closeTaskModal()">
                                Batal
                            </x-secondary-button>

                            <x-primary-button id="submitBtn">
                                Simpan
                            </x-primary-button>
                        </div>
                        
                        <!-- Delete Button (Only shown during edit) -->
                        <button type="button" id="deleteBtn" class="hidden inline-flex justify-center items-center px-4 py-2 border border-transparent text-xs font-bold uppercase tracking-wider rounded-xl text-rose-400 hover:bg-rose-500/10 cursor-pointer" onclick="deleteTask()">
                            Hapus Tugas
                        </button>
                    </div>
                </form>

                <!-- Hidden Delete Form -->
                <form id="deleteTaskForm" action="" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <!-- Drag-and-drop & Modal Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let draggedCard = null;

        document.addEventListener('DOMContentLoaded', () => {
            initializeDragAndDrop();
        });

        function initializeDragAndDrop() {
            const cards = document.querySelectorAll('.task-card');
            const columns = document.querySelectorAll('.task-column');

            cards.forEach(card => {
                card.addEventListener('dragstart', handleDragStart);
                card.addEventListener('dragend', handleDragEnd);
            });

            columns.forEach(column => {
                column.addEventListener('dragover', handleDragOver);
                column.addEventListener('dragenter', handleDragEnter);
                column.addEventListener('dragleave', handleDragLeave);
                column.addEventListener('drop', handleDrop);
            });
        }

        function handleDragStart(e) {
            draggedCard = this;
            this.classList.add('opacity-40', 'scale-95');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.taskId);
        }

        function handleDragEnd() {
            this.classList.remove('opacity-40', 'scale-95');
            document.querySelectorAll('.task-column').forEach(col => {
                col.classList.remove('border-dashed', 'border-indigo-500/40', 'bg-indigo-500/5');
            });
            draggedCard = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDragEnter() {
            this.classList.add('border-dashed', 'border-indigo-500/40', 'bg-indigo-500/5');
        }

        function handleDragLeave() {
            this.classList.remove('border-dashed', 'border-indigo-500/40', 'bg-indigo-500/5');
        }

        async function handleDrop(e) {
            e.preventDefault();
            this.classList.remove('border-dashed', 'border-indigo-500/40', 'bg-indigo-500/5');
            
            const taskId = e.dataTransfer.getData('text/plain');
            const newStatus = this.dataset.status;

            if (draggedCard && draggedCard.dataset.taskId === taskId) {
                // Append card visually
                this.appendChild(draggedCard);
                
                // Show floating notification
                showNotification('Memperbarui status tugas...', 'info');

                try {
                    const response = await fetch(`/tasks/${taskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ status: newStatus })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        showNotification('Status tugas berhasil diperbarui!', 'success');
                        
                        // Update counts
                        document.getElementById('todo-count').textContent = data.counts.todo;
                        document.getElementById('in_progress-count').textContent = data.counts.in_progress;
                        document.getElementById('done-count').textContent = data.counts.done;
                    } else {
                        throw new Error(data.message);
                    }
                } catch (err) {
                    console.error(err);
                    showNotification('Gagal memperbarui status tugas.', 'error');
                    // Reload to revert changes in case of error
                    window.location.reload();
                }
            }
        }

        function showNotification(message, type) {
            const banner = document.getElementById('dragNotification');
            banner.textContent = message;
            
            // Set styles
            banner.className = 'fixed bottom-5 right-5 z-50 p-4 rounded-xl shadow-lg border text-xs font-bold transition-all duration-300 transform';
            
            if (type === 'success') {
                banner.classList.add('bg-emerald-950/90', 'border-emerald-800/40', 'text-emerald-400');
            } else if (type === 'error') {
                banner.classList.add('bg-rose-950/90', 'border-rose-800/40', 'text-rose-450');
            } else {
                banner.classList.add('bg-slate-900/90', 'border-indigo-500/20', 'text-indigo-400');
            }

            banner.classList.remove('hidden', 'translate-y-10', 'opacity-0');
            
            // Auto hide
            setTimeout(() => {
                banner.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => banner.classList.add('hidden'), 300);
            }, 3000);
        }

        function openAddTaskModal() {
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('taskForm').action = "{{ route('tasks.store') }}";
            document.getElementById('modalTitle').textContent = "Tambah Tugas Baru";
            document.getElementById('task_id').value = '';
            document.getElementById('task_title').value = '';
            document.getElementById('task_description').value = '';
            document.getElementById('task_priority').value = 'medium';
            document.getElementById('task_due_date').value = '';
            
            // Clear checked documents
            document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = false);
            
            document.getElementById('deleteBtn').classList.add('hidden');
            document.getElementById('taskModal').classList.remove('hidden');
        }

        function openEditTaskModal(task) {
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('taskForm').action = `/tasks/${task.id}`;
            document.getElementById('modalTitle').textContent = "Edit Rincian Tugas";
            
            document.getElementById('task_id').value = task.id;
            document.getElementById('task_title').value = task.title;
            document.getElementById('task_description').value = task.description || '';
            document.getElementById('task_priority').value = task.priority;
            
            if (task.due_date) {
                // Format YYYY-MM-DD
                const date = new Date(task.due_date);
                const formattedDate = date.toISOString().split('T')[0];
                document.getElementById('task_due_date').value = formattedDate;
            } else {
                document.getElementById('task_due_date').value = '';
            }

            // Sync checked documents
            document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = false);
            if (task.documents && task.documents.length) {
                task.documents.forEach(doc => {
                    const cb = document.getElementById(`doc-checkbox-${doc.id}`);
                    if (cb) cb.checked = true;
                });
            }

            document.getElementById('deleteBtn').classList.remove('hidden');
            document.getElementById('taskModal').classList.remove('hidden');
        }

        function closeTaskModal() {
            document.getElementById('taskModal').classList.add('hidden');
        }

        function deleteTask() {
            const taskId = document.getElementById('task_id').value;
            if (taskId && confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
                const form = document.getElementById('deleteTaskForm');
                form.action = `/tasks/${taskId}`;
                form.submit();
            }
        }
    </script>
</x-app-layout>
