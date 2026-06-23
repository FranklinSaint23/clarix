<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'description',
        'thumbnail', 'level', 'language',
        'price', 'published', 'duration_minutes',
    ];

    protected $casts = [
        'published'        => 'boolean',
        'price'            => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // ─── Slug automatique ─────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    // ─── Relations ────────────────────────────────────────────────────────

    /** Instructeur propriétaire */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Chapitres du cours (ordonnés) */
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    /** Toutes les leçons via les chapitres */
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    /** Quiz du cours */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /** Inscriptions */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Apprenants inscrits */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('status', 'completed_at', 'paid_amount')
                    ->withTimestamps();
    }

    /** Sujets du forum */
    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : asset('images/course-placeholder.jpg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->price > 0
            ? number_format($this->price, 2) . ' FCFA'
            : 'Gratuit';
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
    
}
