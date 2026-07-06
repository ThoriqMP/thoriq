<div draggable="true" 
     data-task-id="{{ $task->id }}" 
     onclick="openEditTaskModal({{ json_encode($task->load('documents')) }})"
     class="task-card bg-slate-900/30 p-4.5 rounded-xl shadow-lg border border-white/5 hover:border-indigo-500/20 cursor-grab active:cursor-grabbing hover:shadow-indigo-950/20 transition duration-150 select-none">
    
    <!-- Priority & Actions -->
    <div class="flex justify-between items-center mb-2.5">
        <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full uppercase tracking-wider border
            @if($task->priority === 'high') bg-rose-500/10 text-rose-450 border-rose-500/20
            @elseif($task->priority === 'medium') bg-amber-500/10 text-amber-450 border-amber-500/20
            @else bg-blue-500/10 text-blue-450 border-blue-500/20 @endif">
            {{ $task->priority }}
        </span>
        
        <!-- Drag Handle Indicator -->
        <svg class="w-4 h-4 text-slate-700 hover:text-slate-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16" />
        </svg>
    </div>

    <!-- Title & Description -->
    <h4 class="font-bold text-xs.5 text-slate-200 leading-snug line-clamp-2">
        {{ $task->title }}
    </h4>
    
    @if($task->description)
        <p class="text-[11px] text-slate-500 font-medium mt-1 line-clamp-2 leading-relaxed">
            {{ $task->description }}
        </p>
    @endif

    <!-- Attachments & Due Date -->
    <div class="mt-4 pt-3.5 border-t border-white/5 flex flex-wrap gap-2 justify-between items-center text-xs">
        
        <!-- Document Attachments Count -->
        <div class="flex items-center space-x-1.5 text-slate-500 font-semibold text-[10px]">
            @if($task->documents->count() > 0)
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                <span>{{ $task->documents->count() }} berkas</span>
            @endif
        </div>

        <!-- Due Date -->
        @if($task->due_date)
            @php
                $isOverdue = $task->due_date->isPast() && $task->status !== 'done';
            @endphp
            <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold flex items-center border
                @if($isOverdue) bg-rose-500/10 text-rose-450 border-rose-500/20
                @else text-slate-450 bg-white/5 border-white/5 @endif">
                <svg class="w-3 h-3 mr-1 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $task->due_date->isoFormat('D MMM YYYY') }}
            </span>
        @endif

    </div>
</div>
