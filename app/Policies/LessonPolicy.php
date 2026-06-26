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
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        if ($lesson->is_free) {
            return true;
        }

        return $user->isEnrolledIn($lesson->chapter->course_id);
    }
}