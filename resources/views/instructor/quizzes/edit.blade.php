@extends('layouts.app')

@section('title', 'Modifier le quiz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-8 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">Mes cours</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $quiz->course) }}" class="hover:text-primary transition">{{ Str::limit($quiz->course->title, 30) }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Modifier le quiz</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-10">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Modifier le quiz</h1>
        <p class="text-sm text-on-surface-variant mt-2">Quiz : <span class="font-medium text-primary">{{ $quiz->title }}</span></p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
        <form action="{{ route('instructor.courses.quizzes.update', [$quiz->course, $quiz]) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf @method('PUT')

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $quiz->title) }}" class="input-field w-full" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">Description</label>
                <textarea name="description" rows="3" class="input-field w-full resize-none">{{ old('description', $quiz->description) }}</textarea>
            </div>

            <!-- Paramètres (grille 2 colonnes) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Score de réussite (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="pass_score" value="{{ old('pass_score', $quiz->pass_score) }}" class="input-field w-full" min="0" max="100" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Temps limite (minutes)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" class="input-field w-full" min="1" placeholder="Illimité">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Tentatives max <span class="text-red-500">*</span></label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" class="input-field w-full" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Leçon associée</label>
                    <select name="lesson_id" class="input-field w-full">
                        <option value="">-- Aucune --</option>
                        @foreach($quiz->course->lessons as $lesson)
                            <option value="{{ $lesson->id }}" @selected($quiz->lesson_id == $lesson->id)>{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Options -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="show_answers" value="1" @checked($quiz->show_answers) class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                    <span class="text-sm text-on-surface">Afficher les réponses après soumission</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('instructor.courses.edit', $quiz->course) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition-all duration-200 shadow-md font-medium">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>

    <!-- Section Questions -->
    <div class="mt-12" x-data="quizAI()">

        <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
            <h2 class="text-xl font-display font-bold flex items-center gap-2">
                <i class="fas fa-question-circle text-primary"></i> Questions du quiz
            </h2>
            <div class="flex gap-3">
                <!-- Bouton Générer avec IA -->
                <button @click="open = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:opacity-90 transition text-sm font-medium shadow">
                    <i class="fas fa-robot"></i> Générer avec l'IA
                </button>
                <a href="{{ route('instructor.questions.create', $quiz) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl hover:bg-primary/20 transition text-sm font-medium">
                    <i class="fas fa-plus-circle"></i> Ajouter manuellement
                </a>
            </div>
        </div>

        <!-- Modal génération IA -->
        <div x-show="open" x-transition.opacity
             class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
             @click.self="open = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        <h3 class="font-display font-bold text-lg">Générer des questions avec l'IA</h3>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nombre de questions</label>
                        <select x-model="count" class="input-field w-full">
                            <option value="3">3 questions</option>
                            <option value="5" selected>5 questions</option>
                            <option value="8">8 questions</option>
                            <option value="10">10 questions</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Contenu / sujet <span class="font-normal text-on-surface-variant">(optionnel — l'IA utilisera le titre du quiz sinon)</span>
                        </label>
                        <textarea x-model="content" rows="5" placeholder="Collez ici le contenu de la leçon, ou décrivez le sujet du quiz..."
                                  class="input-field w-full resize-none text-sm"></textarea>
                    </div>

                    <div x-show="message"
                         :class="success ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                         class="rounded-lg border p-3 text-sm flex items-center gap-2">
                        <i :class="success ? 'fas fa-check-circle text-green-600' : 'fas fa-exclamation-circle text-red-600'"></i>
                        <span x-text="message"></span>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="open = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-medium hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button @click="generate()"
                            :disabled="loading"
                            class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-medium hover:opacity-90 disabled:opacity-50 transition flex items-center justify-center gap-2">
                        <span x-show="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <i x-show="!loading" class="fas fa-magic"></i>
                        <span x-text="loading ? 'Génération...' : 'Générer'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function quizAI() {
        return {
            open: false,
            loading: false,
            content: '',
            count: 5,
            message: '',
            success: false,

            async generate() {
                this.loading = true;
                this.message = '';

                try {
                    const res = await fetch('{{ route('ai.quiz.generate', $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ content: this.content, count: this.count }),
                    });

                    const data = await res.json();

                    if (!res.ok || data.error) {
                        this.success = false;
                        this.message = data.error || 'Erreur lors de la génération.';
                    } else {
                        this.success = true;
                        this.message = data.message;
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } catch (e) {
                    this.success = false;
                    this.message = 'Erreur réseau : ' + e.message;
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    </script>
    @endpush

    <!-- Liste des questions (suite) -->
    <div class="mt-0">

        @if($quiz->questions->count())
            <div class="space-y-6">
                @foreach($quiz->questions as $question)
                    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-surface-low/30 border-b border-outline/20 flex flex-wrap justify-between items-center gap-3">
                            <div class="flex-1">
                                <p class="font-display font-semibold text-lg">{{ $question->question_text }}</p>
                                <div class="flex flex-wrap gap-4 mt-2 text-sm text-on-surface-variant">
                                    <span class="flex items-center gap-1"><i class="fas fa-star text-primary"></i> Points : {{ $question->points }}</span>
                                    <span class="flex items-center gap-1"><i class="fas fa-tag text-primary"></i> Type : 
                                        @if($question->type == 'single') Choix unique
                                        @elseif($question->type == 'multiple') Choix multiples
                                        @else Vrai/Faux @endif
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('instructor.questions.edit', [$quiz, $question]) }}" class="text-primary hover:text-primary/80 text-sm font-medium transition flex items-center gap-1">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('instructor.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium transition flex items-center gap-1" onclick="return confirm('Supprimer cette question ?')">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="px-6 py-4 text-sm text-on-surface-variant">
                            <div class="space-y-2">
                                @foreach($question->answers as $answer)
                                    <div class="flex items-start gap-3">
                                        <i class="fas {{ $answer->is_correct ? 'fa-check-circle text-secondary mt-0.5' : 'fa-circle text-on-surface-variant/30 mt-0.5' }}"></i>
                                        <span>{{ $answer->answer_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @if($question->explanation)
                                <div class="mt-4 text-xs bg-primary-fixed/20 p-3 rounded-lg">
                                    <i class="fas fa-info-circle text-primary"></i> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-outline/20 shadow-sm p-10 text-center">
                <i class="fas fa-question-circle text-5xl text-primary/30 mb-4 block"></i>
                <p class="text-on-surface-variant">Aucune question pour ce quiz.</p>
                <a href="{{ route('instructor.questions.create', $quiz) }}" class="inline-block mt-4 text-primary hover:underline">Ajouter une question →</a>
            </div>
        @endif
    </div>
</div>
@endsection