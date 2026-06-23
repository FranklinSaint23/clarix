<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
{
    $query = Course::published()->with('instructor');

    if ($request->filled('level')) {
        $query->where('level', $request->level);
    }

    if ($request->filled('price')) {
        if ($request->price === 'free') {
            $query->where('price', 0);
        } elseif ($request->price === 'paid') {
            $query->where('price', '>', 0);
        }
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $courses = $query->paginate(12)->appends($request->query());

    // Si requête AJAX, retourner uniquement la grille
    if ($request->ajax()) {
        return view('courses._partials.course_grid', compact('courses'));
    }

    return view('courses.index', compact('courses'));
}

    public function show(Course $course)
    {
        if (!$course->published) abort(404);

        $user = auth()->user();
        $isEnrolled = $user ? $user->isEnrolledIn($course->id) : false;
        $progress = $isEnrolled ? $user->progressIn($course) : 0;

        $course->load(['chapters.lessons', 'instructor']);
        return view('courses.show', compact('course', 'isEnrolled', 'progress'));
    }
}