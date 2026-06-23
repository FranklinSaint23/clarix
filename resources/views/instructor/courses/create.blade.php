@extends('layouts.app')

@section('title', 'Créer un cours')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- En-tête avec retour -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-on-surface-variant mb-2">
            <a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">
                <i class="fas fa-arrow-left mr-1"></i> Mes cours
            </a>
            <span>/</span>
            <span>Nouveau cours</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Créer un cours</h1>
        <p class="text-sm text-on-surface-variant mt-1">Partagez votre expertise avec le monde.</p>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Titre -->
        <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
            <label class="block text-sm font-semibold text-on-surface mb-1">Titre du cours <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" 
                   class="input-field w-full text-base" 
                   placeholder="Ex: Maîtrisez Laravel 11" 
                   required autofocus>
            <p class="text-xs text-on-surface-variant mt-1">Un titre accrocheur attire plus d’étudiants.</p>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
            <label class="block text-sm font-semibold text-on-surface mb-1">Description</label>
            <textarea name="description" rows="6" 
                      class="input-field w-full resize-none" 
                      placeholder="Décrivez le contenu, les objectifs et les prérequis...">{{ old('description') }}</textarea>
            <p class="text-xs text-on-surface-variant mt-1">Utilisez des paragraphes clairs pour faciliter la lecture.</p>
        </div>

        <!-- Niveau et Langue -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm min-h-[130px]">
                <label class="block text-sm font-semibold text-on-surface mb-1">Niveau</label>
                <select name="level" class="input-field w-full">
                    <option value="beginner">🌱 Débutant</option>
                    <option value="intermediate">📘 Intermédiaire</option>
                    <option value="advanced">🚀 Avancé</option>
                </select>
            </div>
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm min-h-[130px]">
                <label class="block text-sm font-semibold text-on-surface mb-1">Langue</label>
                <select name="language" class="input-field w-full">
                    <option value="fr">🇫🇷 Français</option>
                    <option value="en">🇬🇧 Anglais</option>
                </select>
            </div>
        </div>

        <!-- Prix et Miniature (avec hauteur minimale fixe pour éviter le décalage) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm min-h-[160px]">
                <label class="block text-sm font-semibold text-on-surface mb-1">Prix (FCFA)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" 
                       class="input-field w-full" placeholder="0 = gratuit">
                <p class="text-xs text-on-surface-variant mt-1">Laissez 0 pour un cours gratuit.</p>
            </div>
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm min-h-[160px]">
                <label class="block text-sm font-semibold text-on-surface mb-1">Miniature</label>
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="cursor-pointer bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-lg transition text-sm font-medium">
                        Choisir une image
                        <input type="file" id="thumbnail_input" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(event)">
                    </label>
                    <span id="file_name" class="text-sm text-on-surface-variant">Aucun fichier</span>
                </div>
                <div id="thumbnail_preview" class="mt-3 hidden">
                    <p class="text-xs text-on-surface-variant mb-1">Aperçu :</p>
                    <img id="preview_img" class="w-28 h-28 object-cover rounded-xl border border-outline/20 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Publication -->
        <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="published" value="1" id="published" class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                <span class="text-sm text-on-surface">Publier immédiatement</span>
            </label>
            <p class="text-xs text-on-surface-variant mt-2 ml-8">Les cours non publiés restent en brouillon et ne sont pas visibles par les étudiants.</p>
        </div>

        <!-- Actions (boutons plus grands) -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
            <a href="{{ route('instructor.courses.index') }}" 
               class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition text-base font-medium">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" 
                    class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md">
                <i class="fas fa-save"></i> Créer le cours
            </button>
        </div>
    </form>
</div>

<script>
    function previewThumbnail(event) {
        const file = event.target.files[0];
        const fileNameSpan = document.getElementById('file_name');
        const previewDiv = document.getElementById('thumbnail_preview');
        const previewImg = document.getElementById('preview_img');
        
        if (file) {
            fileNameSpan.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            fileNameSpan.textContent = 'Aucun fichier';
            previewDiv.classList.add('hidden');
        }
    }
</script>
@endsection