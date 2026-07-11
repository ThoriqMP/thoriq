<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Document;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'priority'      => 'required|in:low,medium,high',
            'due_date'      => 'nullable|date',
            'document_ids'  => 'nullable|array',
            'document_ids.*'=> 'exists:documents,id',
        ]);

        // Get next position for the 'todo' status of this project
        $nextPosition = Task::where('project_id', $validated['project_id'])
            ->where('status', 'todo')
            ->count();

        $task = Task::create([
            'project_id'  => $validated['project_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'due_date'    => $validated['due_date'],
            'status'      => 'todo',
            'position'    => $nextPosition,
        ]);

        if (!empty($validated['document_ids'])) {
            $task->documents()->attach($validated['document_ids']);
        }

        return back()->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'priority'      => 'required|in:low,medium,high',
            'due_date'      => 'nullable|date',
            'document_ids'  => 'nullable|array',
            'document_ids.*'=> 'exists:documents,id',
        ]);

        $task->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'],
            'due_date'    => $validated['due_date'],
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
            'status'    => 'required|in:todo,in_progress,done',
            'taskIds'   => 'nullable|array',
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

    /**
     * Assign a task to a user (all roles can do this)
     */
    public function assign(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Prevent duplicate assignments
        $exists = TaskAssignment::where('task_id', $task->id)
            ->where('assigned_to', $validated['user_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Pengguna ini sudah ditugaskan ke task tersebut.');
        }

        TaskAssignment::create([
            'task_id'     => $task->id,
            'assigned_to' => $validated['user_id'],
            'assigned_by' => Auth::id(),
        ]);

        // Send notification to assigned user (if not self-assigning)
        if ($validated['user_id'] != Auth::id()) {
            $assigner = Auth::user()->name;
            $taskTitle = $task->title;
            $projectTitle = $task->project?->name ?? 'Workspace';

            NotificationController::send(
                $validated['user_id'],
                'task_assigned',
                "Anda ditugaskan ke task baru",
                "{$assigner} menugaskan Anda ke task \"{$taskTitle}\" dalam proyek \"{$projectTitle}\".",
                route('projects.show', $task->project_id)
            );
        }

        return back()->with('success', 'Anggota berhasil ditugaskan ke task ini!');
    }

    /**
     * Remove a task assignment
     */
    public function unassign(Task $task, User $user)
    {
        TaskAssignment::where('task_id', $task->id)
            ->where('assigned_to', $user->id)
            ->delete();

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }

    /**
     * Post a comment on a task (all roles can do this)
     */
    public function comment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'body'    => $validated['body'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Delete a comment (only by comment author or Treasury)
     */
    public function destroyComment(TaskComment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
