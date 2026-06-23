@extends('layouts.app')

@section('title', 'Modifier le cours')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <div class="mb-6 text-sm text-on-surface-variant">
        <a href="{{ route('instructor.courses.index') }}" class="hover:text-primary transition">
            <i class="fas fa-arrow-left mr-1"></i> Mes cours
        </a>
        <span class="mx-2">/</span>
        <span>Modifier : {{ $course->title }}</span>
    </div>

    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Modifier le cours</h1>
        <p class="text-sm text-on-surface-variant mt-1">Modifiez les informations générales, puis gérez les chapitres, leçons et quiz ci-dessous.</p>
    </div>

    <!-- Formulaire des informations générales -->
    <form action="{{ route('instructor.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <!-- Carte informations générales -->
        <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
            <h2 class="text-lg font-display font-semibold mb-4">Informations générales</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Titre du cours <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $course->title) }}" class="input-field w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Description</label>
                    <textarea name="description" rows="5" class="input-field w-full resize-none">{{ old('description', $course->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Carte Niveau / Langue / Prix / Miniature -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
                <label class="block text-sm font-medium text-on-surface mb-1">Niveau</label>
                <select name="level" class="input-field w-full">
                    <option value="beginner" @selected($course->level == 'beginner')>🌱 Débutant</option>
                    <option value="intermediate" @selected($course->level == 'intermediate')>📘 Intermédiaire</option>
                    <option value="advanced" @selected($course->level == 'advanced')>🚀 Avancé</option>
                </select>
            </div>
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
                <label class="block text-sm font-medium text-on-surface mb-1">Langue</label>
                <select name="language" class="input-field w-full">
                    <option value="fr" @selected($course->language == 'fr')>🇫🇷 Français</option>
                    <option value="en" @selected($course->language == 'en')>🇬🇧 Anglais</option>
                </select>
            </div>
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
                <label class="block text-sm font-medium text-on-surface mb-1">Prix (FCFA)</label>
                <input type="number" name="price" value="{{ old('price', $course->price) }}" step="0.01" min="0" class="input-field w-full" placeholder="0 = gratuit">
            </div>
            <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
                <label class="block text-sm font-medium text-on-surface mb-1">Miniature</label>
                <div class="flex items-center gap-3 flex-wrap">
                    <label class="cursor-pointer bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-lg transition text-sm font-medium">
                        Choisir une image
                        <input type="file" id="thumbnail_input" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(event)">
                    </label>
                    <span id="file_name" class="text-sm text-on-surface-variant">Aucun fichier</span>
                </div>
                <div id="thumbnail_preview" class="mt-3 {{ $course->thumbnail ? '' : 'hidden' }}">
                    <p class="text-xs text-on-surface-variant mb-1">Aperçu actuel :</p>
                    <img id="preview_img" src="{{ $course->thumbnail_url }}" class="w-28 h-28 object-cover rounded-xl border border-outline/20 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Carte Publication -->
        <div class="bg-white rounded-2xl border border-outline/20 p-5 shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="published" value="1" id="published" @checked($course->published) class="w-5 h-5 text-primary rounded border-outline/30 focus:ring-primary">
                <span class="text-sm text-on-surface">Publié (visible dans le catalogue)</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
            <a href="{{ route('instructor.courses.index') }}" 
               class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition text-base font-medium">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" 
                    class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-105 transition-all duration-200 shadow-md">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>

    <!-- Section Chapitres et Leçons -->
    <div class="mt-12 mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-display font-bold flex items-center gap-2">
                <i class="fas fa-layer-group text-primary"></i> Structure du cours
            </h2>
            <a href="{{ route('instructor.courses.chapters.create', $course) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl hover:bg-primary/20 transition text-sm font-medium">
                <i class="fas fa-plus-circle"></i> Ajouter un chapitre
            </a>
        </div>

        @if($course->chapters->count())
            <div class="space-y-4">
                @foreach($course->chapters as $chapter)
                    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden transition hover:shadow-md">
                        <!-- En-tête du chapitre -->
                        <div class="flex flex-wrap justify-between items-center gap-3 px-5 py-3 bg-surface-low/30 border-b border-outline/20">
                            <div class="flex-1">
                                <h3 class="font-display font-semibold text-lg">{{ $chapter->title }}</h3>
                                @if($chapter->description)
                                    <p class="text-sm text-on-surface-variant">{{ $chapter->description }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('instructor.courses.chapters.edit', [$course, $chapter]) }}" 
                                   class="text-primary hover:text-primary/80 text-sm font-medium transition flex items-center gap-1">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button type="button" 
                                        class="delete-item-btn text-red-600 hover:text-red-700 text-sm font-medium transition flex items-center gap-1"
                                        data-url="{{ route('instructor.courses.chapters.destroy', [$course, $chapter]) }}"
                                        data-name="{{ $chapter->title }}"
                                        data-type="chapitre">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </div>
                        </div>
                        <!-- Liste des leçons -->
                        <div class="p-4">
                            @if($chapter->lessons->count())
                                <ul class="divide-y divide-outline/20">
                                    @foreach($chapter->lessons as $lesson)
                                        <li class="py-3 flex flex-wrap justify-between items-center gap-3">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-play-circle text-primary text-sm"></i>
                                                <div>
                                                    <span class="font-medium">{{ $lesson->title }}</span>
                                                    @if($lesson->is_free)
                                                        <span class="ml-2 text-[10px] bg-secondary-fixed text-secondary px-2 py-0.5 rounded-full">Gratuit</span>
                                                    @endif
                                                    @if($lesson->type == 'video')
                                                        <span class="ml-2 text-[10px] bg-surface-high text-on-surface-variant px-2 py-0.5 rounded-full">Vidéo</span>
                                                    @elseif($lesson->type == 'text')
                                                        <span class="ml-2 text-[10px] bg-surface-high text-on-surface-variant px-2 py-0.5 rounded-full">Texte</span>
                                                    @elseif($lesson->type == 'mixed')
                                                        <span class="ml-2 text-[10px] bg-surface-high text-on-surface-variant px-2 py-0.5 rounded-full">📹 + 📝 Mixte</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-3 text-sm">
                                                <a href="{{ route('instructor.chapters.lessons.edit', [$chapter, $lesson]) }}" class="text-primary hover:underline flex items-center gap-1">
                                                    <i class="fas fa-edit text-xs"></i> Modifier
                                                </a>
                                                <button type="button" 
                                                        class="delete-item-btn text-red-600 hover:underline flex items-center gap-1 text-sm"
                                                        data-url="{{ route('instructor.chapters.lessons.destroy', [$chapter, $lesson]) }}"
                                                        data-name="{{ $lesson->title }}"
                                                        data-type="leçon">
                                                    <i class="fas fa-trash-alt text-xs"></i> Supprimer
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-on-surface-variant py-2">Aucune leçon pour ce chapitre.</p>
                            @endif
                            <div class="mt-3 pt-2 border-t border-outline/20">
                                <a href="{{ route('instructor.chapters.lessons.create', $chapter) }}" 
                                   class="inline-flex items-center gap-1 text-sm text-primary hover:underline">
                                    <i class="fas fa-plus-circle"></i> Ajouter une leçon
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-outline/20 shadow-sm p-8 text-center">
                <i class="fas fa-folder-open text-5xl text-primary/30 mb-3 block"></i>
                <h3 class="text-lg font-display font-semibold mb-1">Aucun chapitre</h3>
                <p class="text-sm text-on-surface-variant">Commencez par créer votre premier chapitre pour organiser votre cours.</p>
                <a href="{{ route('instructor.courses.chapters.create', $course) }}" class="inline-block mt-4 text-primary hover:underline">Ajouter un chapitre →</a>
            </div>
        @endif
    </div>

    <!-- Section Quiz -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-display font-bold flex items-center gap-2">
                <i class="fas fa-question-circle text-primary"></i> Quiz du cours
            </h2>
            <a href="{{ route('instructor.courses.quizzes.create', $course) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-xl hover:bg-primary/20 transition text-sm font-medium">
                <i class="fas fa-plus-circle"></i> Ajouter un quiz
            </a>
        </div>
        <div class="bg-white rounded-2xl border border-outline/20 shadow-sm overflow-hidden">
            @if($course->quizzes->count())
                <ul class="divide-y divide-outline/20">
                    @foreach($course->quizzes as $quiz)
                        <li class="px-5 py-4 flex flex-wrap justify-between items-center gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-alt text-primary"></i>
                                <div>
                                    <span class="font-medium">{{ $quiz->title }}</span>
                                    @if($quiz->lesson_id)
                                        <span class="ml-2 text-xs text-on-surface-variant">(lié à une leçon)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-3 text-sm">
                                <a href="{{ route('instructor.courses.quizzes.edit', [$course, $quiz]) }}" class="text-primary hover:underline flex items-center gap-1">
                                    <i class="fas fa-edit text-xs"></i> Modifier
                                </a>
                                <button type="button" 
                                        class="delete-item-btn text-red-600 hover:underline flex items-center gap-1 text-sm"
                                        data-url="{{ route('instructor.courses.quizzes.destroy', [$course, $quiz]) }}"
                                        data-name="{{ $quiz->title }}"
                                        data-type="quiz">
                                    <i class="fas fa-trash-alt text-xs"></i> Supprimer
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-5 py-8 text-center">
                    <i class="fas fa-question-circle text-4xl text-primary/30 mb-2 block"></i>
                    <p class="text-on-surface-variant">Aucun quiz pour ce cours.</p>
                    <a href="{{ route('instructor.courses.quizzes.create', $course) }}" class="inline-block mt-2 text-primary hover:underline">Ajouter un quiz →</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6 transform transition-all">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-trash-alt text-red-500"></i>
            </div>
            <h3 class="text-lg font-display font-semibold">Confirmer la suppression</h3>
        </div>
        <p class="text-on-surface-variant text-sm mb-6">
            Êtes-vous sûr de vouloir supprimer <strong id="deleteItemName"></strong> ?<br>
            Cette action est irréversible.
        </p>
        <div class="flex justify-end gap-3">
            <button id="cancelDeleteBtn" class="px-4 py-2 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition">
                Annuler
            </button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition">
                    Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Prévisualisation de la miniature (inchangée)
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

    // Gestion de la modale de suppression
    const modal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteItemNameSpan = document.getElementById('deleteItemName');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    document.querySelectorAll('.delete-item-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const name = this.dataset.name;
            const type = this.dataset.type;
            deleteItemNameSpan.textContent = `${type} "${name}"`;
            deleteForm.action = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
</script>
@endsection