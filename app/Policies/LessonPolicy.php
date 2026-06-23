<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;

class LessonPolicy
{
    /**
     * Vérifier si l'utilisateur peut voir une leçon.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // L'utilisateur doit être inscrit au cours de cette leçon
        return $user->isEnrolledIn($lesson->chapter->course_id);
    }
}