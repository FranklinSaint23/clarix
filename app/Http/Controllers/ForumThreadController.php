<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumThread;
use Illuminate\Http\Request;

class ForumThreadController extends Controller
{
    private function checkAccess(Course $course)
    {
        $user = auth()->user();
        // Autoriser si l'utilisateur est inscrit OU s'il est l'instructeur du cours
        if (!$user->isEnrolledIn($course->id) && $user->id !== $course->user_id) {
            abort(403, 'Vous devez être inscrit à ce cours ou être son instructeur pour accéder au forum.');
        }
    }

    public function index(Course $course)
    {
        $this->checkAccess($course);
        $threads = $course->forumThreads()
            ->with('author', 'latestPost')
            ->latest()
            ->paginate(20);
        return view('forum.index', compact('course', 'threads'));
    }

    public function create(Course $course)
    {
        $this->checkAccess($course);
        return view('forum.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->checkAccess($course);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);
        $thread = $course->forumThreads()->create([
            'user_id' => auth()->id(),
            'title'   => $data['title'],
        ]);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);
        return redirect()->route('courses.forum.show', [$course, $thread])->with('success', 'Sujet créé.');
    }

    public function show(Course $course, ForumThread $thread)
    {
        $this->checkAccess($course);
        $thread->incrementViews();
        $posts = $thread->posts()->with('author')->paginate(20);
        return view('forum.show', compact('course', 'thread', 'posts'));
    }
}