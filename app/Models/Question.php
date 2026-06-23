<?php
// app/Models/Question.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_id', 'question_text', 'type', 'points', 'explanation', 'order',
    ];

    public function quiz()    { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(Answer::class)->orderBy('order'); }

    public function correctAnswers()
    {
        return $this->hasMany(Answer::class)->where('is_correct', true);
    }
}
