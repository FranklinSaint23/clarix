<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'status', 'completed_at', 'paid_amount',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'paid_amount'  => 'decimal:2',
    ];

    // ─── Relations ────────────────────────────────────────────────────────

    public function user()   { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }

    // ─── Calcul progression ───────────────────────────────────────────────

    /**
     * Retourne le % de progression de l'apprenant dans ce cours.
     */
    public function getProgressPercentAttribute(): int
    {
        $totalLessons = $this->course->lessons()->count();

        if ($totalLessons === 0) return 0;

        $completedLessons = LessonProgress::where('user_id', $this->user_id)
            ->where('completed', true)
            ->whereHas('lesson.chapter', fn($q) => $q->where('course_id', $this->course_id))
            ->count();

        return (int) round(($completedLessons / $totalLessons) * 100);
    }
}
