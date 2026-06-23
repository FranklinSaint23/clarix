<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('instructor.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:single,multiple,true_false',
            'points'        => 'required|integer|min:1',
            'explanation'   => 'nullable|string',
            'answers'       => 'required|array|min:2',
            'answers.*.text'=> 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $data['question_text'],
            'type'          => $data['type'],
            'points'        => $data['points'],
            'explanation'   => $data['explanation'],
            'order'         => $quiz->questions()->max('order') + 1,
        ]);

        foreach ($data['answers'] as $index => $ans) {
            $question->answers()->create([
                'answer_text' => $ans['text'],
                'is_correct'  => $ans['is_correct'] ?? false,
                'order'       => $index + 1,
            ]);
        }

        return redirect()->route('instructor.courses.quizzes.edit', [$quiz->course, $quiz])->with('success', 'Question ajoutée.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        return view('instructor.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:single,multiple,true_false',
            'points'        => 'required|integer|min:1',
            'explanation'   => 'nullable|string',
            'answers'       => 'required|array|min:2',
            'answers.*.id'  => 'nullable|exists:answers,id',
            'answers.*.text'=> 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        $question->update([
            'question_text' => $data['question_text'],
            'type'          => $data['type'],
            'points'        => $data['points'],
            'explanation'   => $data['explanation'],
        ]);

        $existingAnswerIds = [];
        foreach ($data['answers'] as $ans) {
            if (!empty($ans['id'])) {
                $answer = Answer::find($ans['id']);
                if ($answer) {
                    $answer->update([
                        'answer_text' => $ans['text'],
                        'is_correct'  => $ans['is_correct'] ?? false,
                    ]);
                    $existingAnswerIds[] = $answer->id;
                }
            } else {
                $answer = $question->answers()->create([
                    'answer_text' => $ans['text'],
                    'is_correct'  => $ans['is_correct'] ?? false,
                    'order'       => $question->answers()->max('order') + 1,
                ]);
                $existingAnswerIds[] = $answer->id;
            }
        }

        $question->answers()->whereNotIn('id', $existingAnswerIds)->delete();

        return redirect()->route('instructor.courses.quizzes.edit', [$quiz->course, $quiz])->with('success', 'Question mise à jour.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('instructor.courses.quizzes.edit', [$quiz->course, $quiz])->with('success', 'Question supprimée.');
    }
}