<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumThread;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ForumPostController extends Controller
{
    public function store(Request $request, Course $course, ForumThread $thread)
    {
        $request->validate(['body' => 'required|string']);

        $post = $thread->posts()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        if ($request->ajax()) {
            $html = View::make('forum._partials.post', [
                'post' => $post,
                'thread' => $thread,
                'course' => $course
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        }

        return redirect()->route('courses.forum.show', [$course, $thread])->with('success', 'Réponse ajoutée.');
    }

    public function markSolution(Course $course, ForumPost $post)
    {
        $thread = $post->thread;
        if ($thread->user_id !== auth()->id()) abort(403);

        $thread->posts()->where('is_solution', true)->update(['is_solution' => false]);
        $post->update(['is_solution' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Solution marquée.');
    }

    public function destroy(Course $course, ForumPost $post)
    {
        if (auth()->id() !== $post->user_id && !auth()->user()->isAdmin() && !auth()->user()->isInstructor()) {
            abort(403);
        }

        $post->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('courses.forum.show', [$course, $post->thread])->with('success', 'Message supprimé.');
    }
}