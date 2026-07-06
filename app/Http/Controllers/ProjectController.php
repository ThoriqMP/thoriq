<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount(['tasks' => function ($query) {
            $query->where('status', '!=', 'done');
        }])->get();

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:50',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dibuat!');
    }

    public function show(Project $project)
    {
        $projects = Project::all(); // for project switcher
        $documents = \App\Models\Document::all(); // for task attachments
        
        $todoTasks = $project->tasks()->with('documents')->where('status', 'todo')->orderBy('position')->get();
        $inProgressTasks = $project->tasks()->with('documents')->where('status', 'in_progress')->orderBy('position')->get();
        $doneTasks = $project->tasks()->with('documents')->where('status', 'done')->orderBy('position')->get();

        return view('projects.show', compact('project', 'projects', 'documents', 'todoTasks', 'inProgressTasks', 'doneTasks'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:50',
        ]);

        $project->update($validated);

        return back()->with('success', 'Proyek berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus!');
    }
}
