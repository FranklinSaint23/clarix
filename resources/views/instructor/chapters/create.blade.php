@extends('layouts.app')

@section('title', 'Ajouter un chapitre')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-8 text-sm text-on-surface-variant">
        <ol class="flex flex-wrap items-center gap-1">
            <li><a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">Mes cours</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li><a href="{{ route('instructor.courses.edit', $course) }}" class="hover:text-primary transition">{{ Str::limit($course->title, 30) }}</a></li>
            <li><i class="fas fa-chevron-right text-xs mx-1"></i></li>
            <li class="text-primary font-medium">Ajouter un chapitre</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="mb-10">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Ajouter un chapitre</h1>
        <p class="text-sm text-on-surface-variant mt-2">Structurez votre cours en sections logiques pour guider vos étudiants.</p>
    </div>

    <!-- Carte formulaire -->
    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm">
        <form action="{{ route('instructor.courses.chapters.store', $course) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Titre du chapitre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       class="input-field w-full text-base" 
                       placeholder="Ex: Introduction à Laravel, Les bases de données..." 
                       required autofocus>
                <p class="text-xs text-on-surface-variant mt-2">Un titre clair et précis aidera vos étudiants à naviguer.</p>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Description <span class="text-on-surface-variant text-xs font-normal">(optionnelle)</span>
                </label>
                <textarea name="description" rows="4" 
                          class="input-field w-full resize-none" 
                          placeholder="Décrivez brièvement ce que les étudiants apprendront dans ce chapitre...">{{ old('description') }}</textarea>
                <p class="text-xs text-on-surface-variant mt-2">Une description attrayante peut motiver l’apprentissage.</p>
            </div>

            <!-- Ordre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Ordre d’affichage
                </label>
                <div class="flex items-center gap-4 flex-wrap">
                    <input type="number" name="order" value="{{ old('order', $course->chapters->count() + 1) }}" 
                           class="input-field w-32" min="1" step="1">
                    <span class="text-sm text-on-surface-variant">
                        Position actuelle : <span class="font-medium text-primary">{{ $course->chapters->count() + 1 }}</span>
                    </span>
                </div>
                <p class="text-xs text-on-surface-variant mt-2">Définit la position de ce chapitre dans le cours (1 = premier).</p>
            </div>

            <!-- Séparateur visuel -->
            <div class="border-t border-outline/20 pt-4"></div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('instructor.courses.edit', $course) }}" 
                   class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                    class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md">
                    <i class="fas fa-save"></i> Créer le chapitre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection