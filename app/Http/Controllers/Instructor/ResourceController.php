<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function create(Lesson $lesson)
    {
        return view('instructor.resources.create', compact('lesson'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|max:51200', // 50 MB
        ]);

        $file = $request->file('file');
        $path = $file->store('resources', 'public');

        Resource::create([
            'lesson_id' => $lesson->id,
            'title'     => $data['title'],
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('instructor.courses.edit', $lesson->chapter->course)
                         ->with('success', 'Ressource ajoutée.');
    }

    public function edit(Lesson $lesson, Resource $resource)
    {
        return view('instructor.resources.edit', compact('lesson', 'resource'));
    }

    public function update(Request $request, Lesson $lesson, Resource $resource)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'nullable|file|max:51200',
        ]);

        $resource->title = $data['title'];

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('resources', 'public');
            $resource->file_path = $path;
            $resource->file_type = $file->getClientOriginalExtension();
            $resource->file_size = $file->getSize();
        }

        $resource->save();

        return redirect()->route('instructor.courses.edit', $lesson->chapter->course)
                         ->with('success', 'Ressource mise à jour.');
    }

    public function destroy(Lesson $lesson, Resource $resource)
    {
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }
        $resource->delete();

        return redirect()->route('instructor.courses.edit', $lesson->chapter->course)
                         ->with('success', 'Ressource supprimée.');
    }
}