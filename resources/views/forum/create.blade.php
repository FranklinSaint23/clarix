@extends('layouts.app')

@section('title', 'Nouveau sujet')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 animate-fade-in">
    <!-- Carte du formulaire -->
    <div class="bg-white rounded-2xl shadow-md border border-outline/20 overflow-hidden">
        <!-- En-tête avec plus de padding vertical -->
        <div class="px-6 py-6 border-b border-outline/20 bg-gradient-to-r from-surface-low to-white">
            <h1 class="text-2xl md:text-3xl font-display font-bold text-primary tracking-tight">Nouveau sujet</h1>
            <p class="text-on-surface-variant text-sm mt-2">Forum – {{ $course->title }}</p>
        </div>

        <form action="{{ route('courses.forum.store', $course) }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            <!-- Champ Titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-on-surface mb-2">
                    Titre du sujet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" 
                       class="input-field w-full transition focus:ring-2 focus:ring-primary/40"
                       placeholder="Ex: Question sur le module 2"
                       required autofocus>
                <p class="text-xs text-on-surface-variant mt-2">Soyez clair et précis, cela aidera la communauté.</p>
            </div>

            <!-- Champ Message -->
            <div>
                <label for="body" class="block text-sm font-medium text-on-surface mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="body" id="body" rows="6" 
                          class="input-field w-full resize-none transition focus:ring-2 focus:ring-primary/40"
                          placeholder="Décrivez votre question ou votre réflexion en détail..."
                          required></textarea>
                <p class="text-xs text-on-surface-variant mt-2">Utilisez @ pour mentionner un autre membre.</p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                <a href="{{ route('courses.forum.index', $course) }}" 
                   class="inline-flex justify-center items-center gap-2 px-5 py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" 
                        class="inline-flex justify-center items-center gap-2 px-5 py-2 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-sm">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endsection