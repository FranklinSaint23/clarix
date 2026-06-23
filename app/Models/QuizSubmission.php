<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quiz_id', 'score', 'passed',
        'attempt', 'time_spent', 'submitted_at',
    ];

    protected $casts = [
        'passed'       => 'boolean',
        'score'        => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────

    public function user()              { return $this->belongsTo(User::class); }
    public function quiz()              { return $this->belongsTo(Quiz::class); }
    public function submissionAnswers() { return $this->hasMany(SubmissionAnswer::class); }

    // ─── Helpers ──────────────────────────────────────────────────────────

    public function getScoreBadgeColorAttribute(): string
    {
        if ($this->score >= 80) return 'success';
        if ($this->score >= 60) return 'warning';
        return 'danger';
    }
}
