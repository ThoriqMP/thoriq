<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tagFilter = $request->input('tag');

        $query = Document::with(['tags', 'tasks']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($tagFilter) {
            $query->whereHas('tags', function ($q) use ($tagFilter) {
                $q->where('tags.id', $tagFilter);
            });
        }

        $documents = $query->latest()->get();
        $tags = Tag::all();

        return view('documents.index', compact('documents', 'tags', 'search', 'tagFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:102400', // max 100MB
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            
            // Store file securely in local disk (private)
            $path = $file->store('documents');

            $document = Document::create([
                'title' => $validated['title'] ?: $originalName,
                'description' => $validated['description'],
                'file_path' => $path,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
            ]);

            if (!empty($validated['tag_ids'])) {
                $document->tags()->attach($validated['tag_ids']);
            }

            return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diunggah!');
        }

        return back()->withErrors(['file' => 'Gagal mengunggah berkas.']);
    }

    public function download(Document $document)
    {
        if (!Storage::exists($document->file_path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return Storage::download($document->file_path, $document->title);
    }

    public function destroy(Document $document)
    {
        // Delete physical file
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Store new tag via AJAX or simple POST
     */
    public function storeTag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'color' => 'required|string|max:50',
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'tag' => $tag,
                'message' => 'Tag berhasil dibuat!'
            ]);
        }

        return back()->with('success', 'Tag berhasil dibuat!');
    }
}
