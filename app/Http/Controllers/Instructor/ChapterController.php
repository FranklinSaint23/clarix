<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function create(Course $course)
    {
        return view('instructor.chapters.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'nullable|integer|min:0',
        ]);

        $data['course_id'] = $course->id;
        $data['order'] = $data['order'] ?? ($course->chapters()->max('order') + 1);

        Chapter::create($data);

        return redirect()->route('instructor.courses.edit', $course)->with('success', 'Chapitre ajouté.');
    }

    public function edit(Course $course, Chapter $chapter)
    {
        return view('instructor.chapters.edit', compact('course', 'chapter'));
    }

    public function update(Request $request, Course $course, Chapter $chapter)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'nullable|integer|min:0',
        ]);

        $chapter->update($data);

        return redirect()->route('instructor.courses.edit', $course)->with('success', 'Chapitre mis à jour.');
    }

    public function destroy(Course $course, Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('instructor.courses.edit', $course)->with('success', 'Chapitre supprimé.');
    }
}