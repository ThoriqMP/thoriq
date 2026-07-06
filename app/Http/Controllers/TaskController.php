<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Document;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        // Get next position for the 'todo' status of this project
        $nextPosition = Task::where('project_id', $validated['project_id'])
            ->where('status', 'todo')
            ->count();

        $task = Task::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'status' => 'todo',
            'position' => $nextPosition,
        ]);

        if (!empty($validated['document_ids'])) {
            $task->documents()->attach($validated['document_ids']);
        }

        return back()->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
        ]);

        // Sync attached documents
        $task->documents()->sync($validated['document_ids'] ?? []);

        return back()->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Update task status & sorting positions via AJAX drag-and-drop
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
            'taskIds' => 'nullable|array',
            'taskIds.*' => 'integer',
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        // If a custom order list is provided, update positions for tasks in the destination column
        if (!empty($validated['taskIds'])) {
            foreach ($validated['taskIds'] as $index => $id) {
                Task::where('id', $id)->update(['position' => $index]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status dan posisi tugas berhasil diperbarui!'
        ]);
    }
}
