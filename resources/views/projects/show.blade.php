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

        {{-- =====================================================
             TASK DETAIL SIDE PANEL (Assignment + Comments)
        ===================================================== --}}
        <div id="taskDetailPanel"
             class="hidden fixed inset-0 z-50 flex items-start justify-end"
             onclick="closeTaskDetail(event)"
        >
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

            <!-- Sliding Panel -->
            <div id="taskDetailContent"
                 class="relative w-full max-w-md h-full bg-slate-950 border-l border-white/5 shadow-2xl overflow-y-auto flex flex-col"
                 onclick="event.stopPropagation()"
            >
                <!-- Panel Header -->
                <div class="sticky top-0 z-10 px-6 py-4 border-b border-white/5 bg-slate-950/90 backdrop-blur-sm flex justify-between items-start">
                    <div>
                        <span id="detailPriority" class="text-[9px] font-extrabold uppercase tracking-widest px-2 py-0.5 rounded-full border mb-1 inline-block"></span>
                        <h3 id="detailTitle" class="font-bold text-base text-white leading-snug"></h3>
                        <p id="detailProject" class="text-xs text-slate-500 mt-0.5"></p>
                    </div>
                    <button onclick="closeTaskDetail()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-white/5 transition cursor-pointer mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Description -->
                <div class="px-6 py-4 border-b border-white/5">
                    <p id="detailDescription" class="text-sm text-slate-400 leading-relaxed"></p>
                </div>

                <!-- Assignees Section -->
                <div class="px-6 py-5 border-b border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3">
                        👥 Anggota Ditugaskan
                    </h4>
                    <!-- Current Assignees -->
                    <div id="detailAssignees" class="space-y-2 mb-4"></div>

                    <!-- Assign Form -->
                    <form id="assignForm" method="POST" class="flex gap-2">
                        @csrf
                        <select name="user_id" id="assignUserSelect"
                            class="flex-1 text-xs bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-slate-200 focus:outline-none focus:border-indigo-500/60 focus:ring-1 focus:ring-indigo-500/20">
                            <option value="">Pilih anggota...</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->name ?? '-' }})</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition cursor-pointer whitespace-nowrap">
                            + Tugaskan
                        </button>
                    </form>
                </div>

                <!-- Comments Section -->
                <div class="px-6 py-5 flex-1">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">
                        💬 Komentar
                    </h4>

                    <!-- Comment List -->
                    <div id="detailComments" class="space-y-4 mb-5"></div>

                    <!-- Comment Form -->
                    <form id="commentForm" method="POST" class="space-y-2">
                        @csrf
                        <div class="flex gap-2.5 items-start">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-[10px] font-bold text-white shrink-0 mt-0.5">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 space-y-2">
                                <textarea name="body" id="commentBody" rows="2"
                                    class="w-full bg-slate-900 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500/60 focus:ring-1 focus:ring-indigo-500/20 resize-none"
                                    placeholder="Tulis komentar..."></textarea>
                                <button type="submit"
                                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                                    Kirim Komentar
                                </button>
                            </div>
                        </div>
                    </form>
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

        // ================================================================
        // TASK DETAIL PANEL — Assignment & Comments
        // ================================================================
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // All task data injected by Laravel (for client-side panel rendering)
        const allTasksData = @json(array_merge(
            $todoTasks->toArray(),
            $inProgressTasks->toArray(),
            $doneTasks->toArray()
        ));

        let currentTaskId = null;

        function openTaskDetail(taskId) {
            const task = allTasksData.find(t => t.id === taskId);
            if (!task) return;
            currentTaskId = taskId;

            // Priority badge
            const prioEl = document.getElementById('detailPriority');
            const prioColors = {
                high: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                medium: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                low: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            };
            prioEl.className = `text-[9px] font-extrabold uppercase tracking-widest px-2 py-0.5 rounded-full border mb-1 inline-block ${prioColors[task.priority] || ''}`;
            prioEl.textContent = task.priority;

            document.getElementById('detailTitle').textContent = task.title;
            document.getElementById('detailProject').textContent = '{{ $project->name }}';
            document.getElementById('detailDescription').textContent = task.description || 'Tidak ada deskripsi.';

            // Render assignees
            renderAssignees(task.assignees || []);

            // Render comments
            renderComments(task.comments || []);

            // Wire assign form
            const assignForm = document.getElementById('assignForm');
            assignForm.action = `/tasks/${taskId}/assign`;

            // Wire comment form
            const commentForm = document.getElementById('commentForm');
            commentForm.action = `/tasks/${taskId}/comments`;

            // Show panel
            document.getElementById('taskDetailPanel').classList.remove('hidden');
        }

        function closeTaskDetail(event) {
            if (event && event.target !== document.getElementById('taskDetailPanel')) return;
            document.getElementById('taskDetailPanel').classList.add('hidden');
            currentTaskId = null;
        }

        function renderAssignees(assignees) {
            const el = document.getElementById('detailAssignees');
            if (!assignees || assignees.length === 0) {
                el.innerHTML = '<p class="text-xs text-slate-600 italic">Belum ada anggota yang ditugaskan.</p>';
                return;
            }
            el.innerHTML = assignees.map(a => `
                <div class="flex items-center justify-between py-1.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                            ${(a.name || '??').substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-200">${a.name}</p>
                            <p class="text-[10px] text-slate-500">${a.role?.name ?? 'No Role'}</p>
                        </div>
                    </div>
                    <form method="POST" action="/tasks/${currentTaskId}/unassign/${a.id}">
                        <input type="hidden" name="_token" value="${CSRF}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-[10px] text-slate-600 hover:text-rose-400 transition cursor-pointer px-2 py-1 hover:bg-rose-500/5 rounded-lg">
                            Hapus
                        </button>
                    </form>
                </div>
            `).join('');
        }

        function renderComments(comments) {
            const el = document.getElementById('detailComments');
            if (!comments || comments.length === 0) {
                el.innerHTML = '<p class="text-xs text-slate-600 italic">Belum ada komentar. Jadilah yang pertama!</p>';
                return;
            }
            el.innerHTML = comments.map(c => `
                <div class="flex gap-2.5 items-start">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-[10px] font-bold text-white shrink-0 mt-0.5 border border-white/5">
                        ${(c.user?.name || '??').substring(0, 2).toUpperCase()}
                    </div>
                    <div class="flex-1 bg-slate-900/60 rounded-xl border border-white/5 px-3 py-2.5">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-200">${c.user?.name ?? 'Unknown'}</span>
                                <span class="text-[9px] px-1.5 py-0.5 bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/10 font-bold">
                                    ${c.user?.role?.name ?? ''}
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-600">${formatDate(c.created_at)}</span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">${escapeHtml(c.body)}</p>
                    </div>
                </div>
            `).join('');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        function escapeHtml(text) {
            return (text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/\n/g, '<br>');
        }
    </script>
</x-app-layout>
