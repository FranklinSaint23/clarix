@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">{{ $quiz->title }}</h1>
        <p class="text-on-surface-variant mt-2">{{ $quiz->description }}</p>
    </div>

    <!-- Informations -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 text-sm text-on-surface-variant">
        <div class="flex items-center gap-4">
            <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} questions</span>
            <span><i class="fas fa-star"></i> Score requis : {{ $quiz->pass_score }}%</span>
            @if($quiz->time_limit)
                <span><i class="fas fa-hourglass-half"></i> Temps limité : <span id="timer" class="font-mono font-bold">--:--</span></span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs">Tentative {{ $attemptsCount + 1 }} / {{ $quiz->max_attempts }}</span>
            <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="bg-primary h-full rounded-full" style="width: {{ (($attemptsCount + 1) / $quiz->max_attempts) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <form id="quiz-form" action="{{ route('quizzes.submit', $quiz) }}" method="POST">
        @csrf

        @foreach($quiz->questions as $index => $question)
            <div class="bg-white rounded-2xl border border-outline/20 shadow-sm mb-6 overflow-hidden">
                <div class="px-5 py-4 bg-surface-low/30 border-b border-outline/20 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-semibold flex items-center justify-center">{{ $index + 1 }}</span>
                        <span class="font-display font-semibold">{{ $question->question_text }}</span>
                    </div>
                    <span class="text-xs text-on-surface-variant">{{ $question->points }} pt(s)</span>
                </div>
                <div class="p-5">
                    @if($question->type == 'single')
                        @foreach($question->answers as $answer)
                            <label class="flex items-start gap-3 cursor-pointer group mb-2">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="mt-0.5 w-4 h-4 text-primary" required>
                                <span class="text-on-surface group-hover:text-primary">{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    @elseif($question->type == 'multiple')
                        @foreach($question->answers as $answer)
                            <label class="flex items-start gap-3 cursor-pointer group mb-2">
                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" class="mt-0.5 w-4 h-4 text-primary rounded">
                                <span class="text-on-surface group-hover:text-primary">{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    @elseif($question->type == 'true_false')
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" class="w-4 h-4 text-primary" required> Vrai
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" class="w-4 h-4 text-primary" required> Faux
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="sticky bottom-4 mt-8 bg-white/90 backdrop-blur-sm rounded-2xl border border-outline/20 shadow-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="text-sm text-on-surface-variant">
                <i class="fas fa-check-circle text-primary"></i> Vérifiez vos réponses avant soumission.
            </div>
            <div class="flex gap-3">
                <button type="reset" class="px-5 py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">Réinitialiser</button>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition shadow-md font-medium">
                    <i class="fas fa-paper-plane mr-2"></i> Soumettre
                </button>
            </div>
        </div>
    </form>
</div>

@if($quiz->time_limit)
<script>
    let timeLimit = {{ $quiz->time_limit * 60 }};
    const timerDisplay = document.getElementById('timer');
    const form = document.getElementById('quiz-form');

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function updateTimer() {
        if (timeLimit <= 0) {
            timerDisplay.textContent = '00:00';
            form.submit();
            return;
        }
        timerDisplay.textContent = formatTime(timeLimit);
        timeLimit--;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();
</script>
@endif
@endsection