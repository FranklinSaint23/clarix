<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // Quiz attaché à un cours
    public function create(Course $course)
    {
        $lessons = $course->lessons;
        return view('instructor.quizzes.create', compact('course', 'lessons'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'lesson_id'    => 'nullable|exists:lessons,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'pass_score'   => 'required|integer|min:0|max:100',
            'time_limit'   => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'show_answers' => 'boolean',
        ]);

        $data['course_id'] = $course->id;
        $data['show_answers'] = $request->has('show_answers');

        $quiz = Quiz::create($data);

        // ✅ Correction : utiliser la route imbriquée
        return redirect()->route('instructor.courses.quizzes.edit', [$course, $quiz])
                         ->with('success', 'Quiz créé. Ajoutez maintenant des questions.');
    }

    // Quiz attaché à une leçon
    public function createForLesson(Lesson $lesson)
    {
        $course = $lesson->course;
        return view('instructor.quizzes.create', compact('course', 'lesson'));
    }

    public function storeForLesson(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'pass_score'   => 'required|integer|min:0|max:100',
            'time_limit'   => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'show_answers' => 'boolean',
        ]);

        $data['course_id'] = $lesson->course_id;
        $data['lesson_id'] = $lesson->id;
        $data['show_answers'] = $request->has('show_answers');

        $quiz = Quiz::create($data);

        return redirect()->route('instructor.courses.quizzes.edit', [$lesson->course, $quiz])
                         ->with('success', 'Quiz créé. Ajoutez maintenant des questions.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        return view('instructor.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $data = $request->validate([
            'lesson_id'    => 'nullable|exists:lessons,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'pass_score'   => 'required|integer|min:0|max:100',
            'time_limit'   => 'nullable|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'show_answers' => 'boolean',
        ]);

        $data['show_answers'] = $request->has('show_answers');

        $quiz->update($data);

        return redirect()->route('instructor.courses.quizzes.edit', [$course, $quiz])
                         ->with('success', 'Quiz mis à jour.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('instructor.courses.edit', $course)
                         ->with('success', 'Quiz supprimé.');
    }
}