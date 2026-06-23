@extends('layouts.app')

@section('title', 'Résultat du quiz')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Résultat du quiz</h1>
        <p class="text-on-surface-variant mt-2">{{ $submission->quiz->title }}</p>
    </div>

    <!-- Score -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm p-6 mb-8 text-center">
        <div class="text-5xl font-bold text-primary mb-3">{{ $submission->score }}%</div>
        @if($submission->passed)
            <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-medium">
                <i class="fas fa-check-circle"></i> Félicitations ! Vous avez réussi.
            </div>
        @else
            <div class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-medium">
                <i class="fas fa-times-circle"></i> Vous n'avez pas atteint le score requis.
            </div>
        @endif
    </div>

    <!-- Délai d'attente si non réussi -->
    @if(!$submission->passed && !$canRetry && $remainingSeconds > 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl p-4 mb-8">
            <div class="flex items-start gap-3">
                <i class="fas fa-hourglass-half text-amber-600 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-amber-800">Prochaine tentative disponible dans</p>
                    <p class="text-2xl font-mono font-bold text-amber-700" id="retry-timer">--:--</p>
                    <p class="text-sm text-amber-700 mt-1">Vous pourrez repasser ce quiz après ce délai.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Corrigé -->
    @if($submission->quiz->show_answers)
        <div class="mt-8">
            <h2 class="text-xl font-display font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-primary"></i> Corrigé
            </h2>
            <div class="space-y-6">
                @foreach($submission->quiz->questions as $question)
                    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 bg-surface-low/30 border-b border-outline/20">
                            <p class="font-display font-semibold">{{ $question->question_text }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">Points : {{ $question->points }}</p>
                        </div>
                        <div class="p-5">
                            @php
                                $userAnswer = $submission->submissionAnswers->where('question_id', $question->id)->first();
                            @endphp

                            @if($question->type == 'single')
                                @foreach($question->answers as $answer)
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="w-5 mt-0.5">
                                            @if($userAnswer && $userAnswer->answer_id == $answer->id)
                                                <i class="fas fa-check-circle text-primary"></i>
                                            @else
                                                <i class="far fa-circle text-on-surface-variant/30"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="{{ $answer->is_correct ? 'text-green-700 font-semibold' : '' }}">
                                                {{ $answer->answer_text }}
                                            </span>
                                            @if($answer->is_correct)
                                                <span class="ml-2 text-xs text-green-600">(Bonne réponse)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($question->type == 'multiple')
                                @foreach($question->answers as $answer)
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="w-5 mt-0.5">
                                            @php
                                                $selected = $userAnswer && in_array($answer->id, json_decode($userAnswer->answer_id ?? '[]'));
                                            @endphp
                                            @if($selected)
                                                <i class="fas fa-check-square text-primary"></i>
                                            @else
                                                <i class="far fa-square text-on-surface-variant/30"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="{{ $answer->is_correct ? 'text-green-700 font-semibold' : '' }}">
                                                {{ $answer->answer_text }}
                                            </span>
                                            @if($answer->is_correct)
                                                <span class="ml-2 text-xs text-green-600">(Bonne réponse)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($question->type == 'true_false')
                                @foreach($question->answers as $answer)
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="w-5 mt-0.5">
                                            @if($userAnswer && $userAnswer->answer_id == $answer->id)
                                                <i class="fas fa-check-circle text-primary"></i>
                                            @else
                                                <i class="far fa-circle text-on-surface-variant/30"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="{{ $answer->is_correct ? 'text-green-700 font-semibold' : '' }}">
                                                {{ $answer->answer_text }}
                                            </span>
                                            @if($answer->is_correct)
                                                <span class="ml-2 text-xs text-green-600">(Bonne réponse)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($question->explanation)
                                <div class="mt-4 text-sm bg-primary-fixed/10 p-3 rounded-lg">
                                    <i class="fas fa-info-circle text-primary mr-1"></i> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="mt-8 flex flex-wrap justify-between items-center gap-3">
        <a href="{{ route('courses.show', $submission->quiz->course) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
            <i class="fas fa-arrow-left"></i> Retour au cours
        </a>
        @if(!$submission->passed && $canRetry)
            <a href="{{ route('quizzes.take', $submission->quiz) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container transition shadow-md font-medium">
                <i class="fas fa-redo-alt"></i> Repasser le quiz
            </a>
        @endif
    </div>
</div>

@if(!$submission->passed && !$canRetry && $remainingSeconds > 0)
<script>
    let remainingSeconds = Math.floor({{ $remainingSeconds }});
    const timerElement = document.getElementById('retry-timer');

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function updateTimer() {
        if (remainingSeconds <= 0) {
            timerElement.textContent = '00:00';
            window.location.reload();
            return;
        }
        timerElement.textContent = formatTime(remainingSeconds);
        remainingSeconds--;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();
</script>
@endif
@endsection