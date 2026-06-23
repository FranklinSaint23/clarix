<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAnswer extends Model
{
    protected $fillable = [
        'quiz_submission_id', 'question_id', 'answer_id', 'is_correct',
    ];

    protected $casts = ['is_correct' => 'boolean'];

    public function submission() { return $this->belongsTo(QuizSubmission::class, 'quiz_submission_id'); }
    public function question()   { return $this->belongsTo(Question::class); }
    public function answer()     { return $this->belongsTo(Answer::class); }
}
