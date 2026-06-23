@extends('layouts.app')

@section('title', 'Créer un quiz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-6 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">Mes cours</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $course) }}" class="hover:text-primary transition">{{ Str::limit($course->title, 30) }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Ajouter un quiz</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Créer un quiz</h1>
        <p class="text-sm text-on-surface-variant mt-2">Pour le cours : <span class="font-medium text-primary">{{ $course->title }}</span></p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm">
        <form action="{{ route('instructor.courses.quizzes.store', $course) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Titre du quiz <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       class="input-field w-full" placeholder="Ex: Quiz sur le module 1" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-1.5">Description <span class="text-on-surface-variant text-xs font-normal">(optionnelle)</span></label>
                <textarea name="description" rows="3" 
                          class="input-field w-full resize-none" 
                          placeholder="Décrivez brièvement l'objectif du quiz...">{{ old('description') }}</textarea>
            </div>

            <!-- Paramètres principaux -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Score de réussite (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="pass_score" value="{{ old('pass_score', 70) }}" 
                           class="input-field w-full" min="0" max="100" required>
                    <p class="text-xs text-on-surface-variant mt-1">Seuil minimum pour valider le quiz.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Temps limite (minutes)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit') }}" 
                           class="input-field w-full" min="1" step="1" placeholder="Illimité">
                    <p class="text-xs text-on-surface-variant mt-1">Laissez vide si pas de limite.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Tentatives max <span class="text-red-500">*</span></label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', 3) }}" 
                           class="input-field w-full" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-1.5">Leçon associée <span class="text-on-surface-variant text-xs font-normal">(optionnel)</span></label>
                    <select name="lesson_id" class="input-field w-full">
                        <option value="">-- Aucune (quiz de cours) --</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" @selected(old('lesson_id') == $lesson->id)>{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-on-surface-variant mt-1">Liez le quiz à une leçon spécifique.</p>
                </div>
            </div>

            <!-- Option d'affichage des réponses -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="show_answers" value="1" id="show_answers" 
                       {{ old('show_answers') ? 'checked' : '' }}
                       class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                <label for="show_answers" class="text-sm text-on-surface cursor-pointer">Afficher les réponses après soumission</label>
            </div>

            <!-- Séparateur -->
            <div class="border-t border-outline/20 pt-4"></div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('instructor.courses.edit', $course) }}" 
                   class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition-all duration-200 shadow-md font-medium">
                    <i class="fas fa-save"></i> Créer le quiz
                </button>
            </div>
        </form>
    </div>
</div>
@endsection