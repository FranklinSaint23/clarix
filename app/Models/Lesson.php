<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id', 'title', 'description', 'type',
        'video_path', 'video_url', 'content',
        'duration_minutes', 'is_free', 'order',
    ];

    protected $casts = [
        'is_free'          => 'boolean',
        'duration_minutes' => 'integer',
    ];

    // ─── Relations ────────────────────────────────────────────────────────

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class, Chapter::class,
            'id', 'id', 'chapter_id', 'course_id'
        );
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    /** Vérifie si l'utilisateur a terminé cette leçon */
    public function isCompletedBy(int $userId): bool
    {
        return $this->progress()
                    ->where('user_id', $userId)
                    ->where('completed', true)
                    ->exists();
    }

    public function getVideoUrlAttribute(): ?string
    {
        if ($this->video_path) {
            return asset('storage/' . $this->video_path);
        }
        return $this->attributes['video_url'] ?? null;
    }
}
