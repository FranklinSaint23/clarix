<?php

namespace App\Http\Controllers;

use App\Models\Lesson;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        // La vérification d'autorisation est déjà effectuée par le middleware 'can:view,lesson'
        // sur la route. Pas besoin de rappeler authorize() ici.
        return view('lessons.show', compact('lesson'));
    }
}