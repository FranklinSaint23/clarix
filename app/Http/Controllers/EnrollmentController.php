<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = auth()->user();

        if (!$course->published) {
            return back()->with('error', 'Ce cours n\'est pas encore disponible.');
        }

        // Empêcher l'instructeur de s'inscrire à son propre cours
        if ($user->id === $course->user_id) {
            return back()->with('error', 'Vous ne pouvez pas vous inscrire à votre propre cours.');
        }

        if ($user->isEnrolledIn($course->id)) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce cours.');
        }

        // Vérification du paiement (temporaire)
        if ($course->price > 0) {
            return back()->with('error', 'Ce cours est payant. Le paiement sera disponible prochainement.');
        }

        $user->enrolledCourses()->attach($course->id, [
            'status' => 'active',
            'paid_amount' => $course->price,
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Inscription réussie !');
    }
}