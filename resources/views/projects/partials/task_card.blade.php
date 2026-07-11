<div draggable="true" 
     data-task-id="{{ $task->id }}" 
     class="task-card group bg-slate-900/50 rounded-xl shadow-lg border border-white/5 hover:border-indigo-500/20 cursor-grab active:cursor-grabbing hover:shadow-indigo-950/20 transition-all duration-150 select-none"
>
    {{-- ============================================================
         MOBILE CARD — Minimalis: hanya judul + badge + tombol detail
    ============================================================ --}}
    <div class="md:hidden flex items-center justify-between px-3.5 py-3 gap-3">
        <!-- Priority dot + title -->
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="shrink-0 w-2 h-2 rounded-full
                @if($task->priority === 'high') bg-rose-500
                @elseif($task->priority === 'medium') bg-amber-400
                @else bg-blue-400 @endif">
            </span>
            <span class="text-xs font-semibold text-slate-200 truncate leading-snug">{{ $task->title }}</span>
        </div>

        <!-- Detail Button (always visible on mobile) -->
        <button type="button"
            onclick="event.stopPropagation(); openTaskDetail({{ $task->id }})"
            class="shrink-0 flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-[10px] font-bold transition cursor-pointer border border-indigo-500/10">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Detail
        </button>
    </div>

    {{-- ============================================================
         DESKTOP CARD — Lengkap dengan semua informasi
    ============================================================ --}}
    <div class="hidden md:block p-4">
        <!-- Priority & Action Buttons -->
        <div class="flex justify-between items-center mb-2.5">
            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full uppercase tracking-wider border
                @if($task->priority === 'high') bg-rose-500/10 text-rose-400 border-rose-500/20
                @elseif($task->priority === 'medium') bg-amber-500/10 text-amber-400 border-amber-500/20
                @else bg-blue-500/10 text-blue-400 border-blue-500/20 @endif">
                {{ $task->priority }}
            </span>
            <div class="flex items-center gap-1.5">
                <!-- Detail Button -->
                <button type="button"
                    onclick="event.stopPropagation(); openTaskDetail({{ $task->id }})"
                    class="opacity-0 group-hover:opacity-100 p-1 rounded-lg text-slate-500 hover:text-indigo-400 hover:bg-indigo-500/10 transition-all cursor-pointer"
                    title="Lihat Detail & Komentar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </button>
                <!-- Edit Button -->
                <button type="button"
                    onclick="event.stopPropagation(); openEditTaskModal({{ json_encode($task->load('documents')) }})"
                    class="opacity-0 group-hover:opacity-100 p-1 rounded-lg text-slate-500 hover:text-white hover:bg-white/10 transition-all cursor-pointer"
                    title="Edit Tugas">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <svg class="w-4 h-4 text-slate-700 group-hover:text-slate-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
                </svg>
            </div>
        </div>

        <!-- Title & Description -->
        <h4 class="font-bold text-sm text-slate-200 leading-snug line-clamp-2">{{ $task->title }}</h4>
        
        @if($task->description)
            <p class="text-[11px] text-slate-500 font-medium mt-1 line-clamp-2 leading-relaxed">
                {{ $task->description }}
            </p>
        @endif

        <!-- Assignees Avatars -->
        @if($task->assignees->count() > 0)
            <div class="mt-3 flex items-center gap-1 flex-wrap">
                @foreach($task->assignees->take(4) as $assignee)
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-[9px] font-bold text-white border border-slate-900 -ml-1 first:ml-0 ring-1 ring-indigo-500/30"
                         title="{{ $assignee->name }} ({{ $assignee->role?->name ?? 'No Role' }})">
                        {{ strtoupper(substr($assignee->name, 0, 2)) }}
                    </div>
                @endforeach
                @if($task->assignees->count() > 4)
                    <div class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center text-[9px] font-bold text-slate-300 -ml-1">
                        +{{ $task->assignees->count() - 4 }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Footer: Doc count, Comments, Due Date -->
        <div class="mt-3 pt-3 border-t border-white/5 flex flex-wrap gap-2 justify-between items-center text-xs">
            <div class="flex items-center gap-2.5">
                @if($task->documents->count() > 0)
                    <div class="flex items-center gap-1 text-slate-500 font-semibold text-[10px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span>{{ $task->documents->count() }}</span>
                    </div>
                @endif
                @if($task->comments->count() > 0)
                    <div class="flex items-center gap-1 text-slate-500 font-semibold text-[10px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>{{ $task->comments->count() }}</span>
                    </div>
                @endif
            </div>

            @if($task->due_date)
                @php $isOverdue = $task->due_date->isPast() && $task->status !== 'done'; @endphp
                <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold flex items-center border
                    @if($isOverdue) bg-rose-500/10 text-rose-400 border-rose-500/20
                    @else text-slate-450 bg-white/5 border-white/5 @endif">
                    <svg class="w-3 h-3 mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $task->due_date->isoFormat('D MMM YYYY') }}
                </span>
            @endif
        </div>
    </div>
</div>
