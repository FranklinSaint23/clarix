<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'bio',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Helpers de rôle ─────────────────────────────────────────────────

    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isInstructor(): bool { return $this->role === 'instructor'; }
    public function isStudent(): bool    { return $this->role === 'student'; }

    // ─── Relations instructeur ────────────────────────────────────────────

    /** Cours créés par cet instructeur */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // ─── Relations apprenant ──────────────────────────────────────────────

    /** Inscriptions aux cours */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Cours auxquels l'apprenant est inscrit */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('status', 'completed_at', 'paid_amount')
                    ->withTimestamps();
    }

    /** Progression par leçon */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    /** Soumissions de quiz */
    public function quizSubmissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    /** Messages forum */
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    // ─── Helpers métier ───────────────────────────────────────────────────

    /** Vérifie si l'utilisateur est inscrit à un cours donné */
    public function isEnrolledIn(int $courseId): bool
    {
        return $this->enrollments()
                    ->where('course_id', $courseId)
                    ->exists();
    }

    /** Retourne le % de progression dans un cours */
    public function progressIn(Course $course): int
    {
        $enrollment = $this->enrollments()
                           ->where('course_id', $course->id)
                           ->first();

        return $enrollment ? $enrollment->progress_percent : 0;
    }

    /** URL de l'avatar */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0d6efd&color=fff';
    }
}
