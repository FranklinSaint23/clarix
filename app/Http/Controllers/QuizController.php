<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\SubmissionAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function take(Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user->isEnrolledIn($quiz->course_id)) {
            abort(403);
        }

        $lastSubmission = $quiz->submissions()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $attemptsCount = $quiz->submissions()
            ->where('user_id', $user->id)
            ->count();

        $waitUntil = null;
        $canRetry = true;

        if ($lastSubmission) {
            $minutesSinceLast = $lastSubmission->created_at->diffInMinutes(now());
            if ($minutesSinceLast < 10) {
                $canRetry = false;
                $waitUntil = $lastSubmission->created_at->addMinutes(10);
                $remainingSeconds = now()->diffInSeconds($waitUntil, false);
            }
        }

        if (!$canRetry) {
            return redirect()->back()->with('error', 'Vous devez attendre ' . ceil($remainingSeconds / 60) . ' minutes avant de pouvoir repasser ce quiz.');
        }

        return view('quizzes.take', compact('quiz', 'attemptsCount', 'waitUntil'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $user = auth()->user();

        $rules = [];
        foreach ($quiz->questions as $question) {
            $rules['answers.' . $question->id] = 'required';
        }
        $request->validate($rules);

        $totalPoints = 0;
        $earnedPoints = 0;
        $submissionAnswers = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $request->input('answers.' . $question->id);
            $isCorrect = false;
            $answerId = null;

            if ($question->type === 'single') {
                $answerId = $userAnswer;
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                if ($correctAnswer && $userAnswer == $correctAnswer->id) {
                    $isCorrect = true;
                }
            } elseif ($question->type === 'multiple') {
                // Pour choix multiples, on ne gère pas encore l'enregistrement détaillé
                // On peut simplement compter la justesse sans stocker les IDs
                $selected = (array) $userAnswer;
                $correctIds = $question->answers()->where('is_correct', true)->pluck('id')->toArray();
                $isCorrect = empty(array_diff($selected, $correctIds)) && empty(array_diff($correctIds, $selected));
                // On ne stocke pas answer_id pour les multiples (laissé null)
            } elseif ($question->type === 'true_false') {
                // Convertir 'true'/'false' en ID de réponse
                $answerText = ($userAnswer === 'true') ? 'Vrai' : 'Faux';
                $selectedAnswer = $question->answers()->where('answer_text', $answerText)->first();
                if ($selectedAnswer) {
                    $answerId = $selectedAnswer->id;
                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                    if ($correctAnswer && $answerId == $correctAnswer->id) {
                        $isCorrect = true;
                    }
                }
            }

            if ($isCorrect) {
                $earnedPoints += $question->points;
            }

            $submissionAnswers[] = [
                'question_id' => $question->id,
                'answer_id'   => $answerId,
                'is_correct'  => $isCorrect,
            ];
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $passed = $score >= $quiz->pass_score;

        $submission = QuizSubmission::create([
            'user_id'      => $user->id,
            'quiz_id'      => $quiz->id,
            'score'        => $score,
            'passed'       => $passed,
            'attempt'      => $quiz->submissions()->where('user_id', $user->id)->count() + 1,
            'submitted_at' => now(),
        ]);

        foreach ($submissionAnswers as $answerData) {
            SubmissionAnswer::create([
                'quiz_submission_id' => $submission->id,
                'question_id'        => $answerData['question_id'],
                'answer_id'          => $answerData['answer_id'],
                'is_correct'         => $answerData['is_correct'],
            ]);
        }

        return redirect()->route('quizzes.result', $submission);
    }

    public function result(QuizSubmission $submission)
    {
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        $lastSubmission = $submission->quiz->submissions()
            ->where('user_id', auth()->id())
            ->latest()
            ->first();
        $canRetry = true;
        $remainingSeconds = 0;
        if ($lastSubmission && $lastSubmission->created_at->diffInMinutes(now()) < 10) {
            $canRetry = false;
            $waitUntil = $lastSubmission->created_at->addMinutes(10);
            $remainingSeconds = now()->diffInSeconds($waitUntil, false);
        }

        return view('quizzes.result', compact('submission', 'canRetry', 'remainingSeconds'));
    }
}