<?php
// app/Models/Quiz.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 'lesson_id', 'title', 'description',
        'pass_score', 'time_limit', 'max_attempts', 'show_answers',
    ];
    protected $casts = ['show_answers' => 'boolean'];

    public function course()      { return $this->belongsTo(Course::class); }
    public function lesson()      { return $this->belongsTo(Lesson::class); }
    public function questions()   { return $this->hasMany(Question::class)->orderBy('order'); }
    public function submissions() { return $this->hasMany(QuizSubmission::class); }

    /** Nombre de tentatives de l'utilisateur pour ce quiz */
    public function attemptsCountFor(int $userId): int
    {
        return $this->submissions()->where('user_id', $userId)->count();
    }

    /** Dernière soumission de l'utilisateur */
    public function lastSubmissionFor(int $userId): ?QuizSubmission
    {
        return $this->submissions()
                    ->where('user_id', $userId)
                    ->latest()
                    ->first();
    }
}
