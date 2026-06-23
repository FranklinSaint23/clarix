<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ForumThread;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return view('search.index', ['q' => $q, 'courses' => collect(), 'lessons' => collect(), 'threads' => collect()]);
        }

        $courses = Course::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        $lessons = Lesson::where('title', 'like', "%{$q}%")
            ->with('chapter.course')
            ->whereHas('chapter.course', fn($sq) => $sq->where('published', true))
            ->limit(10)
            ->get();

        $threads = ForumThread::where('title', 'like', "%{$q}%")
            ->with('course', 'author')
            ->limit(10)
            ->get();

        return view('search.index', compact('q', 'courses', 'lessons', 'threads'));
    }
}
