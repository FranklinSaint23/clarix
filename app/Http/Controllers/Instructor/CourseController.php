<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
   public function index(Request $request)
    {
        $query = auth()->user()->courses();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('published', true);
            } elseif ($request->status === 'draft') {
                $query->where('published', false);
            }
        }

        $courses = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return view('instructor.courses._grid', compact('courses'));
        }

        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('instructor.courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,intermediate,advanced',
            'language'    => 'required|string|size:2',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|max:2048',
            'published'   => 'boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);
        $data['published'] = $request->has('published');

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        Course::create($data);

        return redirect()->route('instructor.courses.index')->with('success', 'Cours créé.');
    }

    public function edit(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,intermediate,advanced',
            'language'    => 'required|string|size:2',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|max:2048',
            'published'   => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $data['published'] = $request->has('published');

        $course->update($data);

        return redirect()->route('instructor.courses.index')->with('success', 'Cours mis à jour.');
    }

    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('instructor.courses.index')->with('success', 'Cours supprimé.');
    }
}