@extends('layouts.app')

@section('title', 'Modifier une question')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-8 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">Mes cours</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $quiz->course) }}" class="hover:text-primary transition">{{ Str::limit($quiz->course->title, 30) }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.quizzes.edit', [$quiz->course, $quiz]) }}" class="hover:text-primary transition">{{ $quiz->title }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Modifier la question</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-10">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Modifier la question</h1>
        <p class="text-sm text-on-surface-variant mt-2">Quiz : <span class="font-medium text-primary">{{ $quiz->title }}</span></p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        <form action="{{ route('instructor.questions.update', [$quiz, $question]) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf @method('PUT')

            <!-- Texte -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Texte de la question <span class="text-red-500">*</span></label>
                <textarea name="question_text" rows="4" class="input-field w-full resize-none" required>{{ old('question_text', $question->question_text) }}</textarea>
            </div>

            <!-- Type et points -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Type</label>
                    <select name="type" class="input-field w-full">
                        <option value="single" @selected($question->type == 'single')>Choix unique</option>
                        <option value="multiple" @selected($question->type == 'multiple')>Choix multiples</option>
                        <option value="true_false" @selected($question->type == 'true_false')>Vrai / Faux</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Points <span class="text-red-500">*</span></label>
                    <input type="number" name="points" value="{{ old('points', $question->points) }}" class="input-field w-full" min="1" required>
                </div>
            </div>

            <!-- Explication -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Explication</label>
                <textarea name="explanation" rows="2" class="input-field w-full resize-none" placeholder="Expliquez pourquoi la bonne réponse est correcte...">{{ old('explanation', $question->explanation) }}</textarea>
            </div>

            <!-- Réponses existantes + ajout dynamique -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-3">Réponses possibles</label>
                <div id="answers-container" class="space-y-3">
                    @foreach($question->answers as $index => $answer)
                        <div class="answer-item flex flex-wrap items-center gap-3">
                            <input type="text" name="answers[{{ $index }}][text]" value="{{ old("answers.$index.text", $answer->answer_text) }}" class="input-field flex-1" placeholder="Texte de la réponse" required>
                            <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                                <input type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" @checked($answer->is_correct) class="w-4 h-4 text-primary rounded border-outline/30 focus:ring-primary">
                                <span class="text-sm">Correcte</span>
                            </label>
                            <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer->id }}">
                            <button type="button" class="remove-answer text-red-500 hover:text-red-700">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-answer" class="inline-flex items-center gap-1 text-primary text-sm mt-3 hover:underline">
                    <i class="fas fa-plus-circle"></i> Ajouter une réponse
                </button>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('instructor.courses.quizzes.edit', [$quiz->course, $quiz]) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition-all duration-200 shadow-md font-medium">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let answerCount = {{ $question->answers->count() }};
    const container = document.getElementById('answers-container');

    function addAnswerRow(index, text = '', isCorrect = false, id = null) {
        const div = document.createElement('div');
        div.className = 'answer-item flex flex-wrap items-center gap-3';
        div.innerHTML = `
            <input type="text" name="answers[${index}][text]" value="${escapeHtml(text)}" class="input-field flex-1" placeholder="Texte de la réponse" required>
            <label class="inline-flex items-center gap-2 cursor-pointer whitespace-nowrap">
                <input type="checkbox" name="answers[${index}][is_correct]" value="1" class="w-4 h-4 text-primary rounded border-outline/30 focus:ring-primary" ${isCorrect ? 'checked' : ''}>
                <span class="text-sm">Correcte</span>
            </label>
            ${id ? `<input type="hidden" name="answers[${index}][id]" value="${id}">` : ''}
            <button type="button" class="remove-answer text-red-500 hover:text-red-700">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        container.appendChild(div);
        div.querySelector('.remove-answer').addEventListener('click', () => div.remove());
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    document.getElementById('add-answer').addEventListener('click', function() {
        addAnswerRow(answerCount, '', false, null);
        answerCount++;
    });

    // Attacher suppression aux réponses existantes
    document.querySelectorAll('.remove-answer').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.closest('.answer-item').remove();
        });
    });
</script>
@endsection